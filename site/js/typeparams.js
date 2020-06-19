//var field_types=[];
//var field_type_loaded=false;
//var websiteroot='';
//var type_obj_id=null;

var typeparams_id;
var typeparams_obj;
var typeparams_box_id;
var typeparams_box_obj;
//var current_params_count=0;

var temp_imagesizeparams=[];
//var temp_imagesizelist=[];
var temp_imagesizelist_string='';
var temp_imagesize_box_id=null;
var temp_imagesize_parambox_id=null;
var temp_imagesize_updateparent=null;

var proversion=false;

function updateParameters()
{
    if(type_obj == null)
        return ;

    var typename=type_obj.value;

    //find the type
    var typeparams=findTheType(typename);

    if(typeparams!=null)
    {

        typeparams_box_obj.innerHTML=renderParamBox(typeparams,typeparams_id,typeparams_obj.value);

        jQuery(function($)
        {
            //container ||
            $(typeparams_box_obj).find(".hasPopover").popover({"html": true,"trigger": "hover focus","container": "body"});
        });

        var param_att=typeparams["@attributes"];
        var rawquotes=false;
        if (typeof(param_att.rawquotes) != "undefined" && param_att.rawquotes=="1")
            rawquotes=true;
            
        var param_array=getParamOptions(typeparams.params,'param');
        updateParamString('fieldtype_param_',1,param_array.length,typeparams_id,null,rawquotes);
    }
    else
        typeparams_box_obj.innerHTML='<p class="msg_error">Unknown Field Type</p>';
}
/*
function updateTypeParams(type_id,typeparams_id_,typeparams_box_id_)//type selection
{
    //type_obj_id=type_id;

    //current_params_count=0;
    type_obj=document.getElementById(type_id);

    typeparams_id=typeparams_id_;
    typeparams_obj=document.getElementById(typeparams_id);

    //typeparams_id=typeparams_id;
    typeparams_box_id=typeparams_box_id_;
    typeparams_box_obj=document.getElementById(typeparams_box_id_);

    if(!field_type_loaded)
    {
        loadTypes(typeparams_box_obj,type_id,typeparams_id,typeparams_box_id_);
    }
    else
    {
        updateParameters();
    }

}
*/

function getParamOptions(param,optionobjectname)
{
    var options=[];

    if (typeof(param)!== "undefined" && typeof(param[optionobjectname]) !== "undefined")
    {
        if (param[optionobjectname].constructor.name != "Array")
            options.push(param[optionobjectname]);
        else
            options=param[optionobjectname];
    }

    return options;
}

function renderParamBox(typeparams,typeparams_box,paramvaluestring)
{
    //current_params_count=0;
    var att=typeparams["@attributes"];

    var result='<h4>'+att.label+'</h4>';
    result+='<p>'+att.description;

    if (typeof(att.helplink) !== "undefined")
		result+=' <a href="'+att.helplink+'" target="_blank">Read more</a>';

    result+='</p>';


    result+='<div class="form-horizontal" style="width:80%;position:relative;">';


    var param_array=getParamOptions(typeparams.params,'param');



    if(param_array.length>0)
        result+='<hr/>';



    var values=parseQuote(paramvaluestring,',',true);

            for(var i=0;i<param_array.length;i++)
            {
                var param=param_array[i];
                var param_att=param["@attributes"];

                if (proversion || typeof(param_att.proversion) === "undefined" || param_att.proversion==="0")
                {

                    result+='<div class="control-group"><div class="control-label">';
                    //required'+param.label+'

                    var label="";
                    if (typeof(param_att)!== "undefined" && typeof(param_att.label) !== "undefined")
                        label=param_att.label;

                    var description="";
                    if (typeof(param_att)!== "undefined" && typeof(param_att.description) !== "undefined")
                        description=param_att.description;

                    result+='<label id="fieldtype_param_'+i+'-lbl" for="fieldtype_param_'+i+'" class="hasPopover" title="" data-content="'+description+'"';
                    result+=' data-original-title="'+label+'"';
                    result+=' >';
                    result+=label;

                    result+='</label>';
                    result+='</div><div class="controls">';

                    var vlu='';
                    if(values.length>i)
                        vlu=values[i];
                        
                    var rawquotes="false";
                    if (typeof(att.rawquotes) != "undefined" && att.rawquotes=="1")
                        rawquotes="true";
                        

                    var attributes='onchange="updateParamString(\'fieldtype_param_\',1,'+param_array.length+',\''+typeparams_box+'\',event,'+rawquotes+');"';

                    result+=renderInputBox('fieldtype_param_'+i,param,vlu,attributes);

                    result+='</div></div>';
                }
            }




    if (!proversion && typeof(att.proversion) !== "undefined" && att.proversion==="1")
    {
        result+='<div id="" class="fieldtype_disable_box"></div>';
        result+='</div>';
        result='<p style="color:red;">This Field Type available in PRO version only.</p>'+result;

    }
    else
    {
        result+='</div>';
    }



        return result;

}

/*
function ct_initPopovers (event)
{
    jQuery(function($)
    {
        //container ||
        $(document).find(".hasPopover").popover({"html": true,"trigger": "hover focus","container": "body"});
    });

}
*/

function getInputType(obj)
{
    for (i = 0; i < obj.childNodes.length; i++)
    {
        if (obj.childNodes[i].tagName == "INPUT")
        {
            return obj.childNodes[i].type;
        }
        if (obj.childNodes[i].tagName == "SELECT")
        {
            return 'select';
        }
    }
    return '';
}

function updateParamString(inputboxid,countlist,countparams,objectid,e,rawquotes)
{
    //objectid is the element id where value will be set

    if(e!=null)
        e.preventDefault();

    var count=0;

    var list=[];

    for(var r=0;r<countlist;r++)
    {
        var params=[];

        for(var i=0;i<countparams;i++)
        {
           var objectname=inputboxid;

           if(r>0)
            objectname+=r+'x';

           objectname+=i;

           var obj=document.getElementById(objectname);

           var t=getInputType(obj);
           var v="";

           if (t === "radio")
           {
               v=getRadioValue(objectname);
           }
           else if (t === "multiselect")
           {
               v=getSelectValues(select).merge(",");
           }
           else
               v=obj.value;

            if(!rawquotes)
            {
                var q=false;
                if(v.indexOf('"')!=-1)
                {
                    v=v.replace('"','&quot;');
                    q=true;
                }

                if(v.indexOf("'")!=-1)
                {
                    v=v.replace("'",'&apos;');
                    q=true;
                }

                if(q || v.indexOf(',')!=-1 || v.indexOf(':')!=-1 )
                    v='"'+v+'"';
            }

           params.push(v);

           if(v!="")
             	count=i+1; //to include all previous parameters even if they are empty
        }

        var tmp_params="";

        var newparams=[];
        if(count>0)
        {
           	for(var i2=0;i2<count;i2++)
        		newparams.push(params[i2]);

            tmp_params=newparams.join(",");
        }

        list.push(tmp_params);
    }

    var tmp_list=list.join(";");

    var typeparams_obj=document.getElementById(objectid);
    typeparams_obj.value=tmp_list;
    typeparams_obj.innerHTML=tmp_list;
    return false;
}

function getSelectValues(select)
{
  var result = [];
  var options = select && select.options;
  var opt;

  for (var i=0, iLen=options.length; i<iLen; i++) {
    opt = options[i];

    if (opt.selected) {
      result.push(opt.value || opt.text);
    }
  }
  return result;
}

function getRadioValue(objectname)
{
    var radios = document.getElementsByName(objectname);
    var v="";
    var length=radios.length;

    for (var i = 0; i < length; i++)
    {
        var id=radios[i].getAttribute('id');
        var label_obj=document.getElementById(id+"_label");
        var label_class=label_obj.getAttribute('class');
        label_class="btn";//label_class.replace(" active","");

        if (radios[i].checked)
        {
            v=radios[i].value;


            var c=label_class+" active";
            if(v=="")
                c+=" active btn-danger";
            else
                c+=" active btn-success";

            label_obj.className=c;

        }
        else
            label_obj.className=label_class;

    }

    return v;
}

function renderInput_Radio(objname,param,value,onchange)
    {
        var param_att=param["@attributes"];

        var result='<fieldset id="'+objname+'" class="btn-group btn-group-yesno radio">';//
        var options= param_att.options.split(",");

                    for(var o=0;o<options.length;o++)
                    {
                        var opt=options[o].split("|");
                        var id=objname+""+o;

                        var c='btn';
                        if(opt[0]==value)
                        {
                            result+='<input type="radio" id="'+id+'" name="'+objname+'" value="'+opt[0]+'" checked="checked" '+onchange+' />';

                            c+=' active';
                        }
                        else
                        {
                            result+='<input type="radio" id="'+id+'" name="'+objname+'" value="'+opt[0]+'" '+onchange+'  />';

                        }

                        if(opt[0]!='' && opt[0]!='0')
                            c+=' btn-success';

                        result+='<label class="'+c+'" for="'+id+'" id="'+id+'_label" >'+opt[1]+'</label>';


                    }

                    result+='</fieldset>';

        return result;
    }

    function renderInput_Multiselect(id,param,values,onchange)
    {
        var options=getParamOptions(param,'option');

        var values_array=values.split(",");

        var result="";

                    result+='<select id="'+id+'" '+onchange+' multiple="multiple">';

                    for(var o=0;o<options.length;o++)
                    {
                        var opt=options[o]["@attributes"];

                        if (proversion || typeof(opt.proversion) === "undefined" || opt.proversion==="0")
                        {
                            if(values!='' && values_array.indexOf(opt.value)!==-1)
                                result+='<option value="'+opt.value+'" selected="selected">'+opt.label+'</option>';
                            else
                                result+='<option value="'+opt.value+'" >'+opt.label+'</option>';
                        }

                    }

                    result+='</select>';

        return result;
    }

    function renderInput_ImageSizeList(id,param,value,attributes)
    {

        var result="";
        temp_imagesizeparams=getParamOptions(param,'sizeparam');
        temp_imagesizelist_string=value;


        temp_imagesize_parambox_id=id;
        temp_imagesize_box_id='temp_imagesize_box';

        result+='<div id="'+temp_imagesize_box_id+'">';
        result+=BuildImageSizeTable();
        result+='</div>';

        temp_imagesize_updateparent=attributes.replace('onchange="updateParamString(','');
        temp_imagesize_updateparent=temp_imagesize_updateparent.replace(');"','');
        temp_imagesize_updateparent=temp_imagesize_updateparent.replace(/[']/g, "");
        temp_imagesize_updateparent=temp_imagesize_updateparent.split(",");


        result+='<input type="text" id="'+id+'" value="'+value+'" style="display:none;width:100%;" '+attributes+'>';// '+onchange+'>';


        return result;
    }

    function BuildImageSizeTable()
	{
            var value=temp_imagesizelist_string;//document.getElementById(temp_imagesize_parambox_id).value;
            var value_array=value.split(";");

			var result='';



            var i;
            var param=null;
            var param_att=null;

            var blank_value=[];

            var count_list=value_array.length;
            if(value==="")
                count_list=0;

            if(count_list>0)
            {
                result+='<table><thead><tr>';
                for(i=0;i<temp_imagesizeparams.length;i++)
                {
                    blank_value.push("");

                    param=temp_imagesizeparams[i];
                    param_att=param["@attributes"];

                    result+='<th>';//<div class="control-group"><div class="control-label">';
                    result+='<label id="size_param_'+i+'-lbl" class="hasPopover" title="" data-content="'+param_att.description+'"';
                    result+=' data-original-title="'+param_att.label+'" >'+param_att.label+'</label>';
                    //result+='</div>';
                    result+='</th>';
                }
                result+='<th></th>';

                result+='</tr></thead>';
                result+='<tbody>';


                var count_params=temp_imagesizeparams.length;

                for(var r=0;r<count_list;r++)
                {
                    var values=value_array[r].split(',');

                    result+='<tr>';
                    for(i=0;i<count_params;i++)
                    {
                        param=temp_imagesizeparams[i];
                        param_att=param["@attributes"];

                        var vlu="";

                        if (values.length>i)
                            vlu=values[i];

                        var id='size_param_';

                        if(r>0)
                            id+=r+'x';

                        id+=i;
                        
                        

                        var attributes='onchange="updateParamString_ImageSizes(\'size_param_\','+count_list+','+count_params+',\''+temp_imagesize_parambox_id+'\',event,false);"';

                        if (typeof(param_att.style) != "undefined")
                            attributes+='style="'+param_att.style+'"';

                        result+='<td style="padding-right:5px;">'+renderInputBox(id,param,vlu,attributes)+'</td>';

                    }
                    result+='<td><div class="btn-wrapper" id="toolbar-delete"><button onclick="deleteImageSize('+r+');" type="button" class="btn btn-small"><span class="icon-delete"></span></button></div></td>';
                    result+='</tr>';

                }

                result+='</tbody>';
                result+='</table>';

            }

            result+='<button onclick=\'addImageSize("'+blank_value.join(",")+'")\' class="btn btn-small btn-success" type="button" style="margin-top:5px;">';
			result+='<span class="icon-new icon-white"></span><span style="margin-left:10px;">Add Image Size</span></button>';//<hr/>

            return result;
	}

    function updateParamString_ImageSizes(sizeparam,countlist,countparams,tempimagesizeparamboxid,e)
    {

        updateParamString(sizeparam,countlist,countparams,tempimagesizeparamboxid,e,false);

        var obj=document.getElementById(temp_imagesize_parambox_id);
        temp_imagesizelist_string=obj.value;

        var sizeparam_=temp_imagesize_updateparent[0];
        var countlist_=temp_imagesize_updateparent[1];
        var countparams_=temp_imagesize_updateparent[2];
        var tempimagesizeparamboxid_=temp_imagesize_updateparent[3];

        updateParamString(sizeparam_,countlist_,countparams_,tempimagesizeparamboxid_,null,false);

    }

    function deleteImageSize(index)
    {
        var obj=document.getElementById(temp_imagesize_parambox_id);
        var value=obj.value;
        var value_array=value.split(";");
        value_array.splice(index, 1);

        temp_imagesizelist_string=value_array.join(';');
        obj.value=temp_imagesizelist_string;

        var obj2=document.getElementById(temp_imagesize_box_id);
        obj2.innerHTML=BuildImageSizeTable();

        var sizeparam_=temp_imagesize_updateparent[0];
        var countlist_=temp_imagesize_updateparent[1];
        var countparams_=temp_imagesize_updateparent[2];
        var tempimagesizeparamboxid_=temp_imagesize_updateparent[3];


        updateParamString(sizeparam_,countlist_,countparams_,tempimagesizeparamboxid_,null,false);
    }

    function addImageSize(vlu)
    {
        var obj=document.getElementById(temp_imagesize_parambox_id);
        var value=obj.value;
        if(value=='')
            temp_imagesizelist_string=value+',';
        else
            temp_imagesizelist_string=value+';';
        obj.value=temp_imagesizelist_string;

        var obj2=document.getElementById(temp_imagesize_box_id);
        obj2.innerHTML=BuildImageSizeTable();

        var sizeparam_=temp_imagesize_updateparent[0];
        var countlist_=temp_imagesize_updateparent[1];
        var countparams_=temp_imagesize_updateparent[2];
        var tempimagesizeparamboxid_=temp_imagesize_updateparent[3];


        updateParamString(sizeparam_,countlist_,countparams_,tempimagesizeparamboxid_,null,false);
    }


    function renderInput_List(id,param,value,onchange)
    {
        var options=getParamOptions(param,'option');
        var result="";

                    result+='<select id="'+id+'" '+onchange+'>';

                    for(var o=0;o<options.length;o++)
                    {
                        var opt=options[o]["@attributes"];

                        if(opt.value==value)
                            result+='<option value="'+opt.value+'" selected="selected">'+opt.label+'</option>';
                        else
                            result+='<option value="'+opt.value+'" >'+opt.label+'</option>';
                    }

                    result+='</select>';

        return result;
    }

    function renderInput_Folder(id,value,onchange)
    {
        //Here we will take existing "folderlist" element (generated by Joomla!) and we replace id, value etc. to make a new one.
        var typebaseobject=document.getElementById("ct_fieldtypeeditor_box");
        var result=typebaseobject.innerHTML;

        result=result.replace('name="ct_fieldtypeeditor"','name="ct_fieldtypeeditor" '+onchange);
        result=result.replace(/ct_fieldtypeeditor/g, id);
        result=result.replace(' style="display: none;"','');

        result=result.replace('value="'+value+'">','value="'+value+'" selected="selected">');

        var p1=result.indexOf('<select');
        var p2=result.indexOf('</select>');

        result=result.substring(p1,p2+9);
        return result;
    }

    function renderInputBox(id,param,vlu,attributes)
    {
        var param_att=param["@attributes"];

        var result='';
        if(param_att.type!=null)
        {
            if(param_att.type==="number")
            {
                if(vlu=='')
                    vlu=param_att.default;

                var extra="";
                if(param.min!=null)
                    extra+=' min="'+param_att.min+'"';

                if(param_att.max!=null)
                    extra+=' max="'+param_att.max+'"';

                if(param_att.min!=null)
                    extra+=' step="'+param_att.step+'"';

                result+='<input type="number" id="'+id+'" value="'+vlu+'" '+extra+' '+attributes+'>';
            }
            else if(param_att.type==="list")
            {
                result=renderInput_List(id,param,vlu,attributes);
            }
            else if(param_att.type==="multiselect")
            {
                result=renderInput_Multiselect(id,param,vlu,attributes);
            }
            else if(param_att.type==="imagesizelist")
            {
                result=renderInput_ImageSizeList(id,param,vlu,attributes);
            }
            else if(param_att.type==="folder")
            {
                result=renderInput_Folder(id,vlu,attributes);
            }
            else if(param_att.type==="radio")
            {
                result=renderInput_Radio(id,param,vlu,attributes);
            }
            else
                result+='<input type="text" id="'+id+'" value="'+vlu+'" '+attributes+'>';
        }
        else
            result+='<input type="text" id="'+id+'" value="'+vlu+'" '+attributes+'>';

        return result;
    }

function findTheType(typename)
{

    for(var i=0;i<field_types.length;i++)
    {

        var n=field_types[i]["@attributes"].ct_name;

        if(n==typename)
        {

            return field_types[i];//["@attributes"];
        }
    }
    return null;
}



// Changes XML to JSON
function xmlToJson(xml) {

	// Create the return object
	var obj = {};

	if (xml.nodeType == 1) { // element
		// do attributes
		if (xml.attributes.length > 0) {
		obj["@attributes"] = {};
			for (var j = 0; j < xml.attributes.length; j++) {
				var attribute = xml.attributes.item(j);
				obj["@attributes"][attribute.nodeName] = attribute.nodeValue;
			}
		}
	} else if (xml.nodeType == 3) { // text
		obj = xml.nodeValue;
	}

	// do children
	if (xml.hasChildNodes()) {
		for(var i = 0; i < xml.childNodes.length; i++) {
			var item = xml.childNodes.item(i);
			var nodeName = item.nodeName;
			if (typeof(obj[nodeName]) == "undefined") {
				obj[nodeName] = xmlToJson(item);
			} else {
				if (typeof(obj[nodeName].push) == "undefined") {
					var old = obj[nodeName];
					obj[nodeName] = [];
					obj[nodeName].push(old);
				}
				obj[nodeName].push(xmlToJson(item));
			}
		}
	}
	return obj;
}

function parseQuote(str,separator,cleanquotes)
{

    var arr = [];
    var quote = false;  // true means we're inside a quoted field
    var c=0;

    // iterate over each character, keep track of current field index (i)
    for (var i = 0; c < str.length; c++) {
        var cc = str[c];
    //    var nc = str[c+1];  // current character, next character
        arr[i] = arr[i] || '';           // create a new array value (start with empty string) if necessary

        // If it's just one quotation mark, begin/end quoted field
        if (cc == '"')
        {
            quote = !quote;

            if(!cleanquotes)
                arr[i] += cc;

            continue;
        }

        // If it's a comma, and we're not in a quoted field, move on to the next field
        if(Array.isArray(separator))
        {
            var found=false
            for (var s = 0; s < separator.length; s++)
            {
                if (cc == separator[s] && !quote) {found=true;break;}
            }

            if (cc == separator[s] && !quote) { ++i; continue; }
        }
        else
        {
            if (cc == separator && !quote) { ++i; continue; }
        }

        // Otherwise, append the current character to the current field
        arr[i] += cc;
    }

    return arr;
}
