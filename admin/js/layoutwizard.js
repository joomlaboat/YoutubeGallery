//This file comes from Custom Tables extension

/*
var tableselector_id="";
var field_box_id=""

var tableselector_obj=null;
var field_box_obj=null;

var current_table_id=0;
var wizardFields=[];
*/
/*
function changeBackIcon()
{
	var obj=document.getElementById("toolbar-back");
	if(obj)
		obj.classList.add('FillLayoutButton');

}

function loadFields(tableselector_id_,field_box_id_)
{


	tableselector_id=tableselector_id_;
	field_box_id=field_box_id_;

	tableselector_obj=document.getElementById(tableselector_id);
	field_box_obj=document.getElementById(field_box_id);

	loadFieldsUpdate();
}

function loadFieldsUpdate()
{
	var tableid=tableselector_obj.value;

	if(tableid!==current_table_id)
	{
		loadFieldsData(tableid);
	}
}

function loadFieldsData(tableid)
{
	current_table_id=0;
	tableid=parseInt(tableid);
	if(isNaN(tableid) || tableid===0)
	{
		field_box_obj.innerHTML='<p>The Table not selected.</p>';
		return;
	}

	field_box_obj.innerHTML='<p>Loading...</p>';

	var url=websiteroot+"/administrator/index.php?option="+ExtensionName+"&view=api&frmt=json&task=getfields&tableid="+tableid;

	if (typeof fetch === "function")
	{
		fetch(url, {method: 'GET',mode: 'no-cors',credentials: 'same-origin' }).then(function(response)
		{
			if(response.ok)
			{
				response.json().then(function(json)
				{
					wizardFields=Array.from(json);
					current_table_id=tableid;
					updateFieldsBox();
				});
			}
			else
			{
				console.log('Network request for products.json failed with response ' + response.status + ': ' + response.statusText);
				tags_box_obj.innerHTML='<p class="msg_error">'+'Network request for products.json failed with response ' + response.status + ': ' + response.statusText+'</p>';
			}
		}).catch(function(err)
		{
			console.log('Fetch Error :-S', err);
		});
	}
	else
	{
		//for IE
		var http = null;
		var params = "";

		if (!http)
		{
		    http = CreateHTTPRequestObject ();   // defined in ajax.js
		}

		if (http)
		{
		    http.open("GET", url, true);
		    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		    http.onreadystatechange = function()
		    {

			    if (http.readyState == 4)
			    {
			        var res=http.response;
					wizardFields=JSON.parse(res);
					current_table_id=tableid;
					updateFieldsBox();
				}
			};
			http.send(params);
		}

	}

}


function updateFieldsBox()
{



	current_table_id=parseInt(current_table_id);
	if(isNaN(current_table_id) || current_table_id===0)
	{
		//field_box_obj.innerHTML='<p>Table not selected. Select Table.</p>';
		return;
	}

	var l=wizardFields.length;
	if(l===0)
	{
		field_box_obj.innerHTML='<div class="FieldTagWizard"><p>There is no Fields in selected table.</p></div>';
		return;
	}

	var result='';

	if(current_layout_type!==0)
	{
		result+='<div class="FieldTagWizard"><p>Dynamic Field Tags that produce Field Titles (Language dependable):</p>';
		result+=renderFieldTags('*','*',[],'titleparams');
		result+='</div>';
	}

	var a=[1,3,4,6,7,8,9,10];

	if(a.indexOf(current_layout_type)!==-1)
	{
		result+='<div class="FieldTagWizard"><p>Dynamic Field Tags that produce Field Values:</p>';
		result+=renderFieldTags('[',']',['dummy'],'valueparams');
		result+='</div>';

		result+='<div class="FieldTagWizard"><p>Dynamic Field Tags that returns pure Field Values (as it stored in database):</p>';
		result+=renderFieldTags('[_value:',']',['string','md5','changetime','creationtime','lastviewtime','viewcount','id','phponadd','phponchange','phponview','server','multilangstring','text','multilangtext','int','float','email','date','filelink','creationtime','dummy'],'');
		result+='</div>';
	}

	if(current_layout_type===2)
	{

		var fieldtypes_to_skip=['log','phponview','phponchange','phponadd','md5','id','server','userid','viewcount','lastviewtime','changetime','creationtime','imagegallery','filebox','dummy'];

		result+='<div class="FieldTagWizard"><p>Dynamic Field Tags that renders Edit Input/Select boxes:<span style="font-weight:bold;color:darkgreen;">(if it\'s not clear, please click <a href="https://joomlaboat.com/support/custom-tables">here</a>)</span></p>';
		result+=renderFieldTags('[',']',fieldtypes_to_skip,'editparams');
		result+='</div>';

		result+='<div class="FieldTagWizard"><p>Dynamic Field Tags that produce Field Values (if the record is alredy created ID!=0):</p>';
		result+=renderFieldTags('|','|',['dummy'],'valueparams');
		result+='</div>';
	}

	if(result==='')
		field_box_obj.innerHTML='<div class="FieldTagWizard"><p>No Field Tags available for this Layout Type</p></div>';
	else
		field_box_obj.innerHTML='<div class="dynamic_values">'+result+'</div>';

	//field_box_obj.innerHTML='<div class="dynamic_values"><p>Available Fields <span>Click on a button to inster to the layout.</span></p>'+result+'</div>';


}

function findFieldObjectByName(fieldname)
{
	var l=wizardFields.length;
	for (var index=0;index<l;index++)
	{
		var field=wizardFields[index];

		if(field.fieldname===fieldname)
			return field;

	}

	return null;
}

function renderFieldTags(startchar,endchar,fieldtypes_to_skip,param_group)
{
	var result='';

	var l=wizardFields.length;



	for (var index=0;index<l;index++)
	{
		var field=wizardFields[index];

		if(fieldtypes_to_skip.indexOf(field.type)===-1)
		{
	        var t=field.fieldname;
			var p=0;
			var alt=field.fieldtitle;

			var button_value="";
			var typeparams=findTheType(field.type);
			if(typeparams!=null)
			{

				var type_att=typeparams["@attributes"];
				alt+=' ('+type_att.label+')';

				if(param_group!='')
				{


					var param_group_object=typeparams[param_group];
					if (typeof(param_group_object) != "undefined")
					{
						var params=getParamOptions(param_group_object.params,'param');
						p=params.length;

						if(p>0)
							t=field.fieldname+':<span>Params</span>';
					}
				}

				button_value=startchar+t+endchar;
			}
			else
			{
				alt+=' (UNKNOW FIELD TYPE)';

				button_value='<span class="text_error">'+startchar+t+endchar+'</span>';
			}

	        result+='<div style="vertical-align:top;display:inline-block;">';
			result+='<div style="display:inline-block;">';
		    result+='<a href=\'javascript:addFieldTag("0","'+startchar+'","'+endchar+'","'+btoa(field.fieldname)+'",'+p+');\' class="btn" alt="'+alt+'" title="'+alt+'">'+button_value+'</a>';
		    result+='</div>';
	                //result+='<div style="display:inline-block;">'+tag.description+'</div>';
	        result+='</div>';
		}

	}

	return result;
}


function showModalFieldTagForm(tagstartchar,tagendchar,tag,top,left,line,positions,isnew)
{
	var modalcontentobj=document.getElementById("layouteditor_modal_content_box");

    var tag_pair=parseQuote(tag,':',false);

    temp_params_tag=tag_pair[0];
	var field=findFieldObjectByName(temp_params_tag);
	if(field==null)
	{
		modalcontentobj.innerHTML='<p>Cannot find the field. Probably the field does not belongs to selected table.</p>';
		showModal();
		return;
	}

	var param_group=getParamGroup(tagstartchar,tagendchar);

	if(param_group==='')
	{
		modalcontentobj.innerHTML='<p>Something went wrong. Field Type Tag should not have any parameters in this Layout Type. Try to reload the page.</p>';
		showModal();
		return;
	}

	var fieldtypeobj=findTheType(field.type);
	if(fieldtypeobj===null)
	{
		modalcontentobj.innerHTML='<p>Something went wrong. Field Type Tag doesnot not have any parameters. Try to reload the page.</p>';
		showModal();
		return;
	}
	var fieldtype_att=fieldtypeobj["@attributes"];

	var group_params_object=fieldtypeobj[param_group];


	var param_array=getParamOptions(group_params_object.params,'param');

    var countparams=param_array.length;

    var paramvaluestring="";
    if(tag_pair.length==2)
        paramvaluestring=tag_pair[1];

    var form_content=getParamEditForm(group_params_object,line,positions,isnew,countparams,tagstartchar,tagendchar,paramvaluestring);

    if(form_content==null)
        return false;

	var result='<h3>Field "<b>'+field.fieldtitle+'</b>"  <span style="font-size:smaller;">(<i>Type: '+fieldtype_att.label+'</i>)</span>';

    if (typeof(fieldtype_att.helplink) !== "undefined")
		result+=' <a href="'+fieldtype_att.helplink+'" target="_blank">Read more</a>';

	result+='</h3>';



    modalcontentobj.innerHTML=result+form_content;

    jQuery(function($)
    {
        //container ||
        $(modalcontentobj).find(".hasPopover").popover({"html": true,"trigger": "hover focus","layouteditor_modal_content_box": "body"});
    });

    updateParamString("fieldtype_param_",1,countparams,"current_tagparameter",null,false);

    showModal();
 }






function addFieldTag(index_unused,tagstartchar,tagendchar,tag,param_count)
{

    var index=0;
    if(param_count>0)
    {
        var cm=codemirror_editors[index];
        var cr=cm.getCursor();

        var positions=[cr.ch,cr.ch];
        var mousepos=cm.cursorCoords(cr,"window");

        showModalFieldTagForm(tagstartchar,tagendchar,atob(tag),mousepos.top,mousepos.left,cr.line,positions,1);
    }
    else
        updateCodeMirror(tagstartchar+atob(tag)+tagendchar);
}


			function FillLayout()
			{

				var editor = codemirror_editors[codemirror_active_index];


				var t = parseInt(document.getElementById("jform_layouttype").value);
				if(isNaN(t) || t===0)
				{
					alert("Type not selected.");
					return;
				}

				var tableid = parseInt(document.getElementById("jform_tableid").value);
				if(isNaN(tableid) || tableid===0)
				{
					alert("Table not selected.");
					return;
				}

				var layout_obj = document.getElementById("jform_layoutcode");
				layout_obj.value=editor.getValue();

				var v=layout_obj.value;
				if(v!=='')
				{
					alert("Layout Content is not empty, delete it first.");
					return;
				}

				switch(t)
				{
					case 1:
						layout_obj.value=getLayout_SimpleCatalog();
					break;

					case 2:
						layout_obj.value=getLayout_Edit();
					break;

					case 3:
						layout_obj.value=getLayout_Record();
					break;

					case 4:
						layout_obj.value=getLayout_Details();
					break;

					case 5:
						layout_obj.value=getLayout_Page();
					break;

					case 6:
						layout_obj.value=getLayout_Item();
					break;

					case 7:
						layout_obj.value=getLayout_Email();
					break;

					case 8:
						layout_obj.value=getLayout_XML();
					break;

					case 9:
						layout_obj.value=getLayout_CSV();
					break;

					case 10:
						layout_obj.value=getLayout_JSON();
					break;


				}

				//'<!-- Automatacally created layout -->\r\n'+
				editor.getDoc().setValue(layout_obj.value);

			}
*/

			function getLayout_Page()
			{
				var result="";
				var l=wizardFields.length;

				result+='<div style="float:right;">{recordcount}</div>\r\n';
				result+='<div style="float:left;">{add}</div>\r\n';
				result+='\r\n';
				result+='<div style="text-align:center;">{print}</div>\r\n';
				result+='<div class="datagrid">\r\n';
				result+='<div>{batchtoolbar:edit,publish,unpublish,refresh,delete}</div>\r\n\r\n';

				result+='<table><thead><tr>';

				var fieldtypes_to_skip=['log','imagegallery','filebox','dummy'];
				var fieldtypes_withsearch=['email','string','multilangstring','text','multilangtext','int','float','sqljoin','records'];

				for (var index=0;index<l;index++)
				{
					var field=wizardFields[index];

					if(fieldtypes_to_skip.indexOf(field.type)===-1)
					{
						if(fieldtypes_withsearch.indexOf(field.type)===-1)
							result+='<td style=\'text-align:center;\'>*'+field.fieldname+'*</td>\r\n';
						else
							result+='<td style=\'text-align:center;\'>*'+field.fieldname+'*<br/>{search:'+field.fieldname+'}</td>\r\n';
					}
				}

				result+='<td style=\'text-align:center;\'>Action<br/>{searchbutton}</td>\r\n';

				result+='</tr></thead>\r\n\r\n';
				result+='<tbody>\r\n\r\n';

				result+='{catalog}\r\n\r\n';

				result+='</tbody>\r\n';
				result+='</table>\r\n';

				result+='</div>\r\n\r\n';
				result+='<br/><div style=\'text-align:center;\'>{pagination}</div>\r\n';

				return result;
			}


			function getLayout_Item()
			{
				var result="";
				var l=wizardFields.length;

				var fieldtypes_to_skip=['log','imagegallery','filebox','dummy'];

				for (var index=0;index<l;index++)
				{
					var field=wizardFields[index];

					if(fieldtypes_to_skip.indexOf(field.type)===-1)
					{
						result+='<td style=\'text-align:center;\'>['+field.fieldname+']</td>\r\n';
					}
				}

				return result;
			}


			function getLayout_SimpleCatalog()
			{
				var result="";
				var l=wizardFields.length;

				result+='<div style="float:right;">{recordcount}</div>\r\n';
				result+='<div style="float:left;">{add}</div>\r\n';
				result+='\r\n';
				result+='<div style="text-align:center;">{print}</div>\r\n';
				result+='<div class="datagrid">\r\n';
				result+='<div>{batchtoolbar:edit,publish,unpublish,refresh,delete}</div>';
				result+='{catalogtable:\r\n';
				result+='"<span style=\'text-align:center;\'>#<br/>{checkbox}</span>":"<span style=\'text-align:center;\'>{id}<br/>{toolbar:checkbox}</span>",\r\n';

				var fieldtypes_to_skip=['log','imagegallery','filebox','dummy'];
				var fieldtypes_withsearch=['email','string','multilangstring','text','multilangtext','int','float','sqljoin','records'];

				for (var index=0;index<l;index++)
				{
					var field=wizardFields[index];

					if(fieldtypes_to_skip.indexOf(field.type)===-1)
					{
						if(fieldtypes_withsearch.indexOf(field.type)===-1)
							result+='"<span style=\'text-align:center;\'>*'+field.fieldname+'*</span>":"<span style=\'text-align:center;\'>['+field.fieldname+']</span>",\r\n';
						else
							result+='"<span style=\'text-align:center;\'>*'+field.fieldname+'*<br/>{search:'+field.fieldname+'}</span>":"<span style=\'text-align:center;\'>['+field.fieldname+']</span>",\r\n';
					}
				}

				result+='"<span style=\'text-align:center;\'>Action<br/>{searchbutton}</span>":"{toolbar:edit,publish,refresh,delete}";\r\n';
				result+='someTableClass\r\n';
				result+='}\r\n';
				result+='</div>\r\n';
				result+='<br/><div style=\'text-align:center;\'>{pagination}</div>\r\n';

				return result;
			}

			function getLayout_Edit()
			{
				var result="";

				var l=wizardFields.length;

				result+='<div class="form-horizontal">\r\n\r\n';

				var fieldtypes_to_skip=['log','phponview','phponchange','phponadd','md5','id','server','userid','viewcount','lastviewtime','changetime','creationtime','imagegallery','filebox','dummy'];

				for (var index=0;index<l;index++)
				{
					var field=wizardFields[index];

					if(fieldtypes_to_skip.indexOf(field.type)===-1)
					{
						result+='\t<div class="control-group">\r\n';
						result+='\t\t<div class="control-label">*'+field.fieldname+'*</div><div class="controls">['+field.fieldname+']</div>\r\n';
						result+='\t</div>\r\n\r\n';
					}
				}

				result+='</div>\r\n';

				result+='\r\n';

				for (var index2=0;index2<l;index2++)
				{
					var field2=wizardFields[index2];

					if(field2.fieldtyue==="dummy")
					{
						result+='<p><span style="color: #FB1E3D; ">*</span> *'+field2.fieldname+'*</p>\r\n';
						break;
					}
				}


				result+='<div style="text-align:center;">{buttons}</div>\r\n';

				return result;
			}

			function getLayout_Details()
			{
				var result="";

				var l=wizardFields.length;

				result+='{gobackbutton}\r\n\r\n<div class="form-horizontal">\r\n\r\n';

				var fieldtypes_to_skip=['log','imagegallery','filebox','dummy'];

				for (var index=0;index<l;index++)
				{
					var field=wizardFields[index];

					if(fieldtypes_to_skip.indexOf(field.type)===-1)
					{
						result+='\t<div class="control-group">\r\n';
						result+='\t\t<div class="control-label">*'+field.fieldname+'*</div><div class="controls">['+field.fieldname+']</div>\r\n';
						result+='\t</div>\r\n\r\n';
					}
				}

				result+='</div>\r\n';

				result+='\r\n';

				return result;
			}

			function getLayout_Email()
			{
				var result="";

				var l=wizardFields.length;

				result+='<p>New form entry registered:</p>\r\n\r\n';

				var fieldtypes_to_skip=['log','imagegallery','filebox','dummy'];

				for (var index=0;index<l;index++)
				{
					var field=wizardFields[index];

					if(fieldtypes_to_skip.indexOf(field.type)===-1)
					{

						result+='\t\t<p>*'+field.fieldname+'*: ['+field.fieldname+']</p>\r\n';

					}
				}


				return result;
			}

			function getLayout_CSV()
			{
								var result="";
				var l=wizardFields.length;

				result+='{catalogtable:\r\n';
				result+='"#":"{id}",\r\n';

				var fieldtypes_to_skip=['log','imagegallery','filebox','dummy'];

				for (var index=0;index<l;index++)
				{
					var field=wizardFields[index];

					if(fieldtypes_to_skip.indexOf(field.type)===-1)
					{
						result+='"*'+field.fieldname+'*":"['+field.fieldname+']"';

						if(index<l-1)
							result+=',\r\n';
						else
							result+=';';
					}
				}

				result+='}\r\n';

				return result;
			}

			function getLayout_JSON()
			{
								var result="";
				var l=wizardFields.length;

				result+='{catalogtable:\r\n';
				result+='"id_":"{id}",\r\n';

				var fieldtypes_to_skip=['log','imagegallery','filebox','dummy'];

				for (var index=0;index<l;index++)
				{
					var field=wizardFields[index];

					if(fieldtypes_to_skip.indexOf(field.type)===-1)
					{
						result+='"'+field.fieldname+'":"['+field.fieldname+']"';

						if(index<l-1)
							result+=',\r\n';
						else
							result+=';';
					}
				}

				result+='}\r\n';

				return result;
			}

			function getLayout_XML()
			{
								var result="";
				var l=wizardFields.length;

				result+='<?xml version="1.0" encoding="utf-8"?>\r\n<document>\r\n{catalogtable:\r\n';


				var fieldtypes_to_skip=['log','imagegallery','filebox','dummy'];

				for (var index=0;index<l;index++)
				{
					var field=wizardFields[index];

					if(fieldtypes_to_skip.indexOf(field.type)===-1)
					{
						var v='\t<field name=\''+field.fieldname+'\' label=\'*'+field.fieldname+'*\'>['+field.fieldname+']</field>\r\n';
						if(index==0)
							result+='"":"<record id=\'{id}\'>\r\n'+v+'"';
						else if(index==l-1)
							result+='"":"'+v+'</record>\r\n"';
						else
							result+='"":"'+v+'"';

						if(index<l-1)
							result+=',';
						else
							result+=';';
					}
				}
				result+='}\r\n</document>';
				return result;
			}

			function getLayout_Record()
			{
				var result="";

				var l=wizardFields.length;

				var fieldtypes_to_skip=['log','dummy'];
				var fieldtypes_to_purevalue=['image','imagegallery','filebox','file'];

				for (var index=0;index<l;index++)
				{
					var field=wizardFields[index];

					if(fieldtypes_to_skip.indexOf(field.type)===-1)
					{
						if(fieldtypes_to_purevalue.indexOf(field.type)===-1)
							result+='\t<div>['+field.fieldname+']</div>\r\n';
						else
							result+='\t<div>[_value:'+field.fieldname+']</div>\r\n';
					}
				}

				return result;
			}
