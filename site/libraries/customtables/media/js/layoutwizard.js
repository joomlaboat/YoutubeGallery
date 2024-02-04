/**
 * CustomTables Joomla! 3.x/4.x/5.x Component and WordPress 6.x Plugin
 * @package Custom Tables
 * @subpackage administrator/components/com_customtables/js/layoutwizard.js
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @copyright Copyright (C) 2018-2024. All Rights Reserved
 * @license GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
 **/

let tableselector_id = "";
let field_box_id = "";
let tableselector_obj = null;
let current_table_id = 0;
let wizardFields = [];
let wizardLayouts = [];
let joomlaVersion = 3;
let languages = [];

//Used in layouteditor.php
function loadLayout(version) {
    joomlaVersion = version;
    let obj = document.getElementById("allLayoutRaw");

    if (obj)
        wizardLayouts = JSON.parse(obj.innerHTML);
}

function openLayoutWizard() {
    FillLayout();
}

//Used in layouteditor.php
function loadFields(tableselector_id_, field_box_id_, CMSType) {
    tableselector_id = tableselector_id_;
    field_box_id = field_box_id_;
    tableselector_obj = document.getElementById(tableselector_id);
    loadFieldsUpdate(CMSType);
}

function loadFieldsUpdate(CMSType) {
    let tableid = tableselector_obj.value;
    if (tableid !== current_table_id)
        loadFieldsData(tableid, CMSType);
}

function loadFieldsData(tableid, CMSType) {
    current_table_id = 0;
    tableid = parseInt(tableid);
    if (isNaN(tableid) || tableid === 0)
        return;//table not selected

    let url = '';
    if (CMSType === 'Joomla') {
        const parts = location.href.split("/administrator/");
        const websiteRoot = parts[0] + "/administrator/";
        url = websiteRoot + "index.php?option=com_customtables&view=api&frmt=json&task=getfields&tableid=" + tableid;
    } else if (CMSType === 'WordPress') {
        let parts = location.href.split("wp-admin/admin.php?");
        url = parts[0] + 'wp-admin/admin.php?page=customtables-api-fields&table=' + tableid;
    } else {
        alert('loadTags: CMS Not Supported.');
        return;
    }

    if (typeof fetch === "function") {

        fetch(url, {method: 'GET', mode: 'no-cors', credentials: 'same-origin'}).then(function (response) {

            if (response.ok) {
                response.json().then(function (json) {

                    wizardFields = Array.from(json);
                    current_table_id = tableid;
                    updateFieldsBox();
                });
            } else {
                console.log('Network request for products.json failed with response ' + response.status + ': ' + response.statusText);
            }

        }).catch(function (err) {
            console.log('Fetch Error :', err);
        });
    } else {
        //for IE
        let params = "";
        let http = CreateHTTPRequestObject();   // defined in ajax.js

        if (http) {
            http.open("GET", url, true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.onreadystatechange = function () {
                if (http.readyState === 4) {
                    let res = http.response;
                    wizardFields = JSON.parse(res);
                    current_table_id = tableid;
                    updateFieldsBox();
                }
            };
            http.send(params);
        }
    }
}

function updateFieldsBox() {

    //let result=renderFieldsBox();
    //result+='<p>Position cursor to the code editor where you want to insert a new dynamic tag and click on the Tag Button.</p>';
    //field_box_obj.innerHTML='';//<div class="dynamic_values">'+result+'</div>';
}

function renderTabs(tabSetId, tabs) {

    if (typeof wp !== 'undefined') {
        return renderTabsWordPress(tabSetId, tabs);
    } else if (typeof Joomla !== 'undefined') {
        return renderTabsJoomla(tabSetId, tabs);
    } else {
        console.log('CMS not supported.');
        return 'CMS not supported.';
    }
}

function activateTabsWordPress(tabClassName) {

    const tabs = document.querySelectorAll('[data-tabs=".gtabs.' + tabClassName + '"]');
    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            // Activate the clicked tab and deactivate others
            tabs.forEach(t => t.classList.remove('nav-tab-active'));
            this.classList.add('nav-tab-active');

            const tabsContentContainer = document.querySelectorAll('.' + tabClassName);
            if (tabsContentContainer.length > 0) {
                const tabsContentDivs = tabsContentContainer[0].querySelectorAll('.gtab');

                tabsContentDivs.forEach(t => t.classList.remove('active'));
                //this.classList.add('active');

                let tabId = this.dataset.tab;
                let tabDiv = document.querySelectorAll(tabId);
                tabDiv.forEach(t => t.classList.add('active'));
            }
        });
    });
}

function renderTabsWordPress(tabSetId, tabs) {

    let buttons = '';
    let divs = '';
    for (let i = 0; i < tabs.length; i++) {
        let tab = tabs[i];

        let cssclass_buttons = "";
        let cssclass_divs = "";
        if (i === 0) {
            cssclass_buttons = ' nav-tab-active';
            cssclass_divs = ' active';
        }
        buttons += '<button data-toggle="tab" data-tabs=".gtabs.' + tabSetId + '" data-tab=".' + tab.id + '-tab' + (i + 1) + '" class="nav-tab' + cssclass_buttons + '" >' + tab.title + '</button>';
        divs += '<div class="gtab' + cssclass_divs + ' ' + tab.id + '-tab' + (i + 1) + '" style="margin-left:-20px;">' + tab.content + '</div>';
    }
    return '<h2 class="nav-tab-wrapper wp-clearfix">' + buttons + '</h2><div class="gtabs ' + tabSetId + '">' + divs + '</div>';
}

function renderTabsJoomla(tabSetId, tabs) {
    // Tabs is the array of tab elements [{"title":"Tab Title","id":"Tab Name","content":"Tab Content"}...]

    if (joomlaVersion < 4) {
        let result_li = '';
        let result_div = '';
        //let activeTabSet=true;

        for (let i = 0; i < tabs.length; i++) {
            let tab = tabs[i];

            let cssclass = "";
            if (i === 0)
                cssclass = "active";

            result_li += '<li' + (cssclass !== '' ? ' class="' + cssclass + '"' : '') + '><a href="#' + tab.id + '" onclick="resizeModalBox();" data-toggle="tab">' + tab.title + '</a></li>';
            result_div += '<div id="' + tab.id + '" class="tab-pane' + (i === 0 ? ' active' : '') + '">' + tab.content + '</div>';
        }
        return '<ul class="nav nav-tabs" >' + result_li + '</ul>\n\n<div class="tab-content" id="' + tabSetId + '">' + result_div + '</div>';
    } else {
        //let result_li = '';
        let result_div = '';

        for (let i = 0; i < tabs.length; i++) {
            let tab = tabs[i];

            let cssClass = "";
            if (i === 0)
                cssClass = "active";

            result_div += '<joomla-tab-element' + (i === 0 ? ' active' : '') + ' style="height:fit-content;overflow-y: auto;overflow-x: none;" id="' + tab.id + '" name="' + tab.title + '">' + tab.content + '</joomla-tab-element>';
        }

        //let result_div_li = '<div role="tablist">' + result_li + '</div>';
        return '<joomla-tab id="' + tabSetId + '" orientation="horizontal" recall="" breakpoint="768">' + result_div + '</joomla-tab>';
    }
}

function replaceOldFieldTitleTagsWithTwigStyle() {

    let editor = getActiveEditor();
    let documentText = editor.getValue();
    let count = 0;
    let changesMade = false;

    //Titles
    for (let i = 0; i < wizardFields.length; i++) {
        let oldFieldTag = '*' + wizardFields[i].fieldname + '*';
        if (documentText.indexOf(oldFieldTag) !== -1)
            count += 1;
    }

    if (count > 0) {
        if (confirm("Found " + count + " old field title tags. Would you like to replace them with Twig style tags?") === true) {
            for (let i = 0; i < wizardFields.length; i++) {
                let oldFieldTag = '*' + wizardFields[i].fieldname + '*';
                let newFieldTag = '{{ ' + wizardFields[i].fieldname + '.title }}';
                documentText = documentText.replace(oldFieldTag, newFieldTag)
                changesMade = true;
            }
        }
    }

    count = 0;

    //values
    for (let i = 0; i < wizardFields.length; i++) {
        let oldFieldTag = '|' + wizardFields[i].fieldname + '|';
        if (documentText.indexOf(oldFieldTag) !== -1)
            count += 1;
    }

    if (count > 0) {
        if (confirm("Found " + count + " old field value tags. Would you like to replace them with Twig style tags?") === true) {
            for (let i = 0; i < wizardFields.length; i++) {
                let oldFieldTag = '|' + wizardFields[i].fieldname + '|';
                let newFieldTag = '{{ ' + wizardFields[i].fieldname + '.value }}';
                documentText = documentText.replace(oldFieldTag, newFieldTag)
                changesMade = true;
            }
        }
    }

    if (changesMade === true) {
        editor.setValue(documentText);
        editor.refresh();
    }
}

function renderFieldsBox() {
    //1 - Simple Catalog
    //2 - Edit Form
    //3 - Record Link
    //4 - Details
    //5 - Catalog Page
    //6 - Catalog Item
    //7 - Email Message
    //8 - XML File
    //9 - CSV File
    //10 - JSON - File

    let tabs = [];

    current_table_id = parseInt(current_table_id);
    if (isNaN(current_table_id) || current_table_id === 0) {
        //field_box_obj.innerHTML='<p>Table not selected. Select Table.</p>';
        return;
    }

    const l = wizardFields.length;
    if (l === 0) {
        //field_box_obj.innerHTML='<div class="FieldTagWizard"><p>There are no Fields in selected table.</p></div>';
        return;
    } else {
        replaceOldFieldTitleTagsWithTwigStyle();
    }

    const a = [1, 3, 4, 6, 7, 8, 9, 10];//Layout Types that may have Field Values.
    const fieldtypes_to_skip = ['log', 'phponview', 'phponchange', 'phponadd', 'md5', 'id', 'server', 'userid', 'viewcount', 'lastviewtime', 'changetime', 'creationtime', 'imagegallery', 'filebox', 'dummy'];

    if (a.indexOf(current_layout_type) !== -1) {
        tabs.push({
            'id': 'layouteditor_fields_value', 'title': 'Field Values',
            'content': '<p>Dynamic Field Tags that produce Field Values:</p>' + renderFieldTags('{{ ', '', ' }}', ['dummy'], 'valueparams') //skip 'dummy'
        });
    }

    //Any Layout Type
    tabs.push({
        'id': 'layouteditor_fields_titles', 'title': 'Field Titles',
        'content': '<p>Dynamic Field Tags that produce Field Titles (Language dependable):</p>' + renderFieldTags('{{ ', '.title', ' }}', [], 'titleparams')
    });
    tabs.push({
        'id': 'layouteditor_fields_labels', 'title': 'Field Labels',
        'content': '<p>Dynamic Field Tags that produce Field Title Label HTML tag (Language dependable):</p>' + renderFieldTags('{{ ', '.label', ' }}', [], 'titleparams')
    });

    if (a.indexOf(current_layout_type) !== -1) {
        tabs.push({
            'id': 'layouteditor_fields_purevalue', 'title': 'Pure Values',
            'content': '<p>Dynamic Field Tags that returns pure Field Values (as it stored in database):</p>' + renderFieldTags('{{ ', '.value', ' }}', ['string', 'md5', 'changetime', 'creationtime', 'lastviewtime', 'viewcount', 'id', 'phponadd', 'phponchange', 'phponview', 'server', 'multilangstring', 'text', 'multilangtext', 'int', 'float', 'email', 'date', 'filelink', 'creationtime', 'dummy'], '')
        });

        tabs.push({
            'id': 'layouteditor_fields_ajaxedit', 'title': 'Edit',
            'content': '<p>Renders input/select box for selected field. It works in all types of layout except Edit Form:</p>' + renderFieldTags('{{ ', '.edit', ' }}', fieldtypes_to_skip, '')
        });
    }

    if (current_layout_type === 2) {
        let fieldtypes_to_skip = ['log', 'phponview', 'phponchange', 'phponadd', 'md5', 'id', 'server', 'userid', 'viewcount', 'lastviewtime', 'changetime', 'creationtime', 'imagegallery', 'filebox', 'dummy'];

        let label = '<p>Dynamic Field Tags that renders an input field where the user can enter data.<span style="font-weight:bold;color:darkgreen;">(more <a href="https://joomlaboat.com/custom-tables-wiki?document=04.-Field-Types" target="_blank">here</a>)</span></p>';
        tabs.push({
            'id': 'layouteditor_fields_edit', 'title': 'Input/Edit',
            'content': label + renderFieldTags('{{ ', '.edit', ' }}', fieldtypes_to_skip, 'editparams')
        });


        tabs.push({
            'id': 'layouteditor_fields_valueineditform', 'title': 'Field Values',
            'content': '<p>Dynamic Field Tags that produce Field Values (if the record is alredy created ID!=0):</p>' + renderFieldTags('{{ ', '', ' }}', ['dummy'], 'valueparams')
        });
    }

    if (tabs.length > 0)
        return renderTabs('layouteditor_fields', tabs);
    else
        return '<div class="FieldTagWizard"><p>No Field Tags available for this Layout Type</p></div>';
}

function findFieldObjectByName(fieldname) {

    let l = wizardFields.length;
    for (let index = 0; index < l; index++) {
        let field = wizardFields[index];
        if (field.fieldname === fieldname)
            return field;
    }
    return null;
}

function renderFieldTags(startChar, postfix, endChar, fieldtypes_to_skip, param_group) {
    let result = '';
    const l = wizardFields.length;

    for (let index = 0; index < l; index++) {
        const field = wizardFields[index];

        if (fieldtypes_to_skip.indexOf(field.type) === -1) {
            let t = field.fieldname + postfix;
            let p = 0;
            let alt = field.fieldtitle;
            let button_value = "";
            const typeparams = findTheType(field.type);
            if (typeparams != null) {

                const type_att = typeparams["@attributes"];
                alt += ' (' + type_att.label + ')';

                if (param_group !== '') {
                    const param_group_object = typeparams[param_group];
                    if (typeof (param_group_object) != "undefined") {
                        const params = getParamOptions(param_group_object.params, 'param');
                        p = params.length;

                        if (p > 0)
                            t += '(<span>Params</span>)';
                    }
                }

                button_value = startChar + t + endChar;
            } else {
                alt += ' (UNKNOWN FIELD TYPE)';

                button_value = '<span class="text_error">' + startChar + t + endChar + '</span>';
            }

            result += '<div style="vertical-align:top;display:inline-block;">';
            result += '<div style="display:inline-block;">';

            if (joomlaVersion < 4)
                result += '<a href=\'javascript:addFieldTag("' + startChar + '","' + postfix + '","' + endChar + '","' + btoa(field.fieldname) + '",' + p + ');\' class="btn" title="' + alt + '">' + button_value + '</a>';
            else
                result += '<a href=\'javascript:addFieldTag("' + startChar + '","' + postfix + '","' + endChar + '","' + btoa(field.fieldname) + '",' + p + ');\' class="btn-primary" title="' + alt + '">' + button_value + '</a>';

            result += '</div>';
            result += '</div>';
        }
    }
    return result;
}

function getParamGroup(tagstartchar, postfix, tagendchar) {
    let param_group = '';
    const a = [1, 3, 4, 6, 7, 8, 9, 10];

    if (postfix === '.title' || (current_layout_type !== 5 && tagstartchar === '*' && tagendchar === '*'))
        param_group = 'titleparams';
    else if (postfix === '.edit' || current_layout_type === 2)
        param_group = 'editparams';
    else if (a.indexOf(current_layout_type) !== -1 && ((tagstartchar === '[' && tagendchar === ']') || (tagstartchar === '{{ ' && tagendchar === ' }}')))
        param_group = 'valueparams';

    return param_group;
}

function showModalTagsList(e) {
    document.getElementById("layouteditor_modal_content_box").innerHTML = do_render_current_TagSets();
    showModal();
}

function showModalDependenciesList(e) {
    document.getElementById("layouteditor_modal_content_box").innerHTML = document.getElementById("dependencies_content").innerHTML;
    showModal();
}

function showModalFieldTagsList(e) {
    document.getElementById("layouteditor_modal_content_box").innerHTML = '<div class="dynamic_values">' + renderFieldsBox() + '</div>';
    showModal();
}

function showModalFieldTagForm(tagStartChar, postfix, tagEndChar, tag, top, left, line, positions, isNew) {
    let modalContentObject = document.getElementById("layouteditor_modal_content_box");
    let paramValueString = "";
    let tag_pair = parseQuote(tag, '(', false)

    if (tag_pair.length > 1) {

        let sub_tag_pair = parseQuote(tag_pair[0], '.', false);
        if (sub_tag_pair.length > 1) {
            temp_params_tag = sub_tag_pair[0].trim();
            postfix = '.' + sub_tag_pair[1].trim();
        } else {
            temp_params_tag = tag_pair[0].trim();
        }

        paramValueString = findTagParameter(tag);
    } else {
        tag_pair = parseQuote(tag, ':', false);
        if (tag_pair.length > 1) {
            if (tag_pair[0] === "_value" || tag_pair[0] === "_edit") {

                if (tag_pair[0] === "_value")
                    postfix = '.value';
                else if (tag_pair[0] === "_edit")
                    postfix = '.edit';

                temp_params_tag = tag_pair[1].trim();

                if (tag_pair.length === 2)
                    paramValueString = tag_pair[1];

            } else {
                temp_params_tag = tag_pair[0].trim();

                let pos1 = tag.indexOf(":");
                paramValueString = tag.substring(pos1 + 1, tag.length);
            }
        } else {

            tag_pair = parseQuote(tag, '.', false);
            if (tag_pair.length > 1) {
                temp_params_tag = tag_pair[0].trim();
                postfix = '.' + tag_pair[1].trim();
            } else {
                temp_params_tag = tag.trim();
            }
        }
    }

    const field = findFieldObjectByName(temp_params_tag);
    if (field == null) {
        modalContentObject.innerHTML = '<p>Cannot find the field. Probably the field does not belong to selected table.</p>';
        showModal();
        return;
    }

    const param_group = getParamGroup(tagStartChar, postfix, tagEndChar);

    if (param_group === '') {
        modalContentObject.innerHTML = '<p>Something went wrong. Field Type Tag should not have any parameters in this Layout Type. Try to reload the page.</p>';
        showModal();
        return;
    }

    const fieldTypeObject = findTheType(field.type);
    if (fieldTypeObject === null) {
        modalContentObject.innerHTML = "<p>Something went wrong. Field Type Tag doesn't have any parameters. Try to reload the page.</p>";
        showModal();
        return;
    }
    const fieldType_att = fieldTypeObject["@attributes"];
    const group_params_object = fieldTypeObject[param_group];

    if (!group_params_object || !group_params_object.params) {
        let cursor_from = {line: line, ch: positions[0]};
        let cursor_to = {line: line, ch: positions[1]};

        let tagPair1 = parseQuote(tag, '(', false);
        let tagPair2 = parseQuote(tagPair1[0], '.', false);
        if ((tagPair1.length > 1 || tagPair2.length > 1) && tagPair2[0] + postfix === tagPair1[0])
            return;

        let result = '{{ ' + tag + postfix + ' }}';

        let editor = getActiveEditor();//codemirror_editors[codemirror_active_index];
        let doc = editor.getDoc();
        doc.replaceRange(result, cursor_from, cursor_to, "");
        return;
    }

    let fieldTypeParametersList = parseQuote(field.typeparams, ",", true);

    const param_array = getParamOptions(group_params_object.params, 'param');
    const countParams = param_array.length;
    const form_content = getParamEditForm(group_params_object, line, positions, isNew, countParams, '{{ ', postfix, ' }}', paramValueString, fieldTypeParametersList);

    if (form_content == null)
        return false;

    let result = '<h3>Field "<b>' + field.fieldtitle + '</b>"  <span style="font-size:smaller;">(<i>Type: ' + fieldType_att.label + '</i>)</span>';

    if (typeof (fieldType_att.helplink) !== "undefined")
        result += ' <a href="' + fieldType_att.helplink + '" target="_blank">Read more</a>';

    result += '</h3>';

    modalContentObject.innerHTML = result + form_content;

    if (joomlaVersion < 4) {
        jQuery(function ($) {
            $(modalContentObject).find(".hasPopover").popover({
                "html": true,
                "trigger": "hover focus",
                "layouteditor_modal_content_box": "body"
            });
        });
    }

    updateParamString("fieldtype_param_", 1, countParams, "current_tagparameter", null, false);
    showModal();
}

//Used in generated html link
function addFieldTag(tagStartChar, postfix, tagEndChar, tag, param_count) {

    let cm = getActiveEditor();

    if (param_count > 0) {
        const cr = cm.getCursor();
        const positions = [cr.ch, cr.ch];
        const mousepos = cm.cursorCoords(cr, "window");

        showModalFieldTagForm(tagStartChar, postfix, tagEndChar, atob(tag), mousepos.top, mousepos.left, cr.line, positions, 1);
    } else {
        updateCodeMirror(tagStartChar + atob(tag) + postfix + tagEndChar);////-----------------todo

        //in case modal window is open
        const modal = document.getElementById('layouteditor_Modal');
        modal.style.display = "none";

        cm.focus();
    }
}

function FillLayout() {
    let editor = getActiveEditor();//codemirror_editors[codemirror_active_index];
    let t = parseInt(document.getElementById("jform_layouttype").value);
    if (isNaN(t) || t === 0) {
        alert("Type not selected.");
        return;
    }

    let tableid = parseInt(document.getElementById("jform_tableid").value);
    if (isNaN(tableid) || tableid === 0) {
        alert("Table not selected.");
        return;
    }

    let layout_obj = document.getElementById(codemirror_active_areatext_id);
    layout_obj.value = editor.getValue();

    let v = layout_obj.value;
    if (v !== '') {
        alert("Layout Content is not empty, delete it first.");
        return;
    }

    switch (t) {
        case 1:
            layout_obj.value = getLayout_SimpleCatalog();
            break;

        case 2:
            layout_obj.value = getLayout_Edit();
            break;

        case 3:
            layout_obj.value = getLayout_Record();
            break;

        case 4:
            layout_obj.value = getLayout_Details();
            break;

        case 5:
            layout_obj.value = getLayout_Page();
            break;

        case 6:
            layout_obj.value = getLayout_Item();
            break;

        case 7:
            layout_obj.value = getLayout_Email();
            break;

        case 8:
            layout_obj.value = getLayout_XML();
            break;

        case 9:
            layout_obj.value = getLayout_CSV();
            break;

        case 10:
            layout_obj.value = getLayout_JSON();
            break;
    }
    editor.getDoc().setValue(layout_obj.value);
}

function getLayout_Page() {

    let result = "";
    //let l = wizardFields.length;

    result += '<style>\r\n';
    result += '.datagrid th{text-align:left;}\r\n';
    result += '.datagrid td{text-align:left;}\r\n';
    result += '</style>\r\n';

    result += '<legend>{{ table.title }}</legend>\r\n';
    result += '<div style="float:right;">{{ html.recordcount }}</div>\r\n';
    result += '<div style="float:left;">{{ html.add }}</div>\r\n';
    result += '\r\n';
    result += '<div style="text-align:center;">{{ html.print }}</div>\r\n';
    result += '<div class="datagrid">\r\n';
    result += '<div>{{ html.batch("edit","publish","unpublish","refresh","delete") }}</div>\r\n\r\n';

    result += '<table>';

    let fieldtypes_to_skip = ['log', 'imagegallery', 'filebox', 'dummy'];
    let fieldtypes_withsearch = ['email', 'string', 'multilangstring', 'text', 'multilangtext', 'sqljoin', 'records', 'user', 'userid', 'int', 'checkbox'];
    let fieldtypes_allowed_to_orderby = ['string', 'email', 'url', 'sqljoin', 'phponadd', 'phponchange', 'int', 'float', 'ordering', 'changetime', 'creationtime', 'date', 'multilangstring', 'customtables', 'userid', 'user'];

    result += renderTableHead(fieldtypes_to_skip, fieldtypes_withsearch, fieldtypes_allowed_to_orderby);

    result += '<tbody>\r\n';

    result += '{{ document.layout("LAYOUT NAME") }}<!-- Please create a "Catalog Item" layout and type the name of that layout instead of LAYOUT NAME -->\r\n';

    result += '</tbody>\r\n';
    result += '</table>\r\n';

    result += '</div>\r\n\r\n';
    result += '<br/><div style=\'text-align:center;\'>{{ html.pagination }}</div>\r\n';

    return result;
}

function getLayout_Item() {
    let result = "";
    let l = wizardFields.length;

    let fieldtypes_to_skip = ['log', 'imagegallery', 'filebox', 'dummy'];
    let user_fieldtypes = ['user', 'userid'];

    //Look for ordering field type
    for (let index = 0; index < l; index++) {
        let field = wizardFields[index];
        if (field.type == 'ordering') {
            result += '<td style="text-align:center;">{{ ' + field.fieldname + ' }}</td>\r\n';
        }
    }

    result += '<td style="text-align:center;">{{ html.toolbar("checkbox") }}</td>\r\n';
    result += '<td style="text-align:center;"><a href="{{ record.link(true) }}">{{ record.id }}</a></td>\r\n';

    let user_field = '';

    for (let index = 0; index < l; index++) {
        let field = wizardFields[index];

        if (field.type != 'ordering' && fieldtypes_to_skip.indexOf(field.type) === -1) {

            let fieldValue = '';
            if (field.type == 'url')
                fieldValue = '<a href="{{ ' + field.fieldname + ' }}" target="_blank">{{ ' + field.fieldname + ' }}</a>';
            else
                fieldValue = '{{ ' + field.fieldname + ' }}';

            result += '<td>' + fieldValue + '</td>\r\n';
        }

        if (user_fieldtypes.indexOf(field.type) !== -1)
            user_field = field.fieldname;
    }

    if (user_field === '') {
        result += '<td>{{ html.toolbar("edit","publish","refresh","delete") }}</td>\r\n';
    } else {
        result += '<td>\r\n';
        result += '\t<!-- The "if" statement is to show the toolbar for the record\'s author only. -->\r\n';
        result += '\t{% if ' + user_field + '.value == {{ user.id }} %} <!-- Where "' + user_field + '" is the user type field name. -->\r\n';
        result += '\t\t<!-- toolbar -->\r\n';
        result += '\t\t{{ html.toolbar("edit","publish","refresh","delete") }}\r\n';
        result += '\t\t<!-- end of toolbar -->\r\n';
        result += '\t{% endif %}\r\n';
        result += '</td>\r\n';
    }
    return '<tr>\r\n' + result + '</tr>\r\n';
}

function getLayout_SimpleCatalog() {

    let result = "";
    let l = wizardFields.length;

    result += '<style>\r\n.datagrid th{text-align:left;}\r\n.datagrid td{text-align:left;}\r\n</style>\r\n';
    result += '<div style="float:right;">{{ html.recordcount }}</div>\r\n';
    result += '<div style="float:left;">{{ html.add }}</div>\r\n';
    result += '\r\n';
    result += '<div style="text-align:center;">{{ html.print }}</div>\r\n';
    result += '<div class="datagrid">\r\n';
    result += '<div>{{ html.batch(\'edit\',\'publish\',\'unpublish\',\'refresh\',\'delete\') }}</div>';
    result += '\r\n';

    let fieldtypes_to_skip = ['log', 'imagegallery', 'filebox', 'dummy'];
    let fieldTypesWithSearch = ['email', 'string', 'multilangstring', 'text', 'multilangtext', 'sqljoin', 'records', 'user', 'userid', 'int', 'checkbox'];
    let fieldtypes_allowed_to_orderby = ['string', 'email', 'url', 'sqljoin', 'phponadd', 'phponchange', 'int', 'float', 'ordering', 'changetime', 'creationtime', 'date', 'multilangstring', 'customtables', 'userid', 'user'];

    result += '\r\n<table>\r\n';

    result += renderTableHead(fieldtypes_to_skip, fieldTypesWithSearch, fieldtypes_allowed_to_orderby);

    result += '\r\n<tbody>';
    result += '\r\n{% block record %}';
    result += '\r\n<tr>\r\n';

    //Look for ordering field type
    for (let index = 0; index < l; index++) {
        let field = wizardFields[index];
        if (field.type == 'ordering') {
            result += '<td style="text-align:center;">{{ ' + field.fieldname + ' }}</td>\r\n';
        }
    }

    result += '<td style="text-align:center;">{{ html.toolbar("checkbox") }}</td>\r\n';
    result += '<td style="text-align:center;"><a href=\'{{ record.link(true) }}\'>{{ record.id }}</a></td>\r\n';

    for (let index = 0; index < l; index++) {
        let field = wizardFields[index];

        if (field.type != 'ordering' && fieldtypes_to_skip.indexOf(field.type) === -1) {

            let fieldValue = '';
            if (field.type == 'url')
                fieldValue = '<a href="{{ ' + field.fieldname + ' }}" target="_blank">{{ ' + field.fieldname + ' }}</a>';
            else
                fieldValue = '{{ ' + field.fieldname + ' }}';

            result += '<td>' + fieldValue + '</td>\r\n';
        }
    }

    result += '<td>{{ html.toolbar("edit","publish","refresh","delete") }}</td>\r\n';

    result += '</tr>';

    result += '\r\n{% endblock %}';
    result += '\r\n</tbody>';
    result += '\r\n</table>\r\n';

    result += '\r\n';
    result += '</div>\r\n';
    result += '<br/><div style=\'text-align:center;\'>{{ html.pagination }}</div>\r\n';
    return result;
}

function renderTableHead(fieldtypes_to_skip, fieldTypesWithSearch, fieldtypes_allowed_to_orderby) {

    let l = wizardFields.length;
    let result = '';

    result += '<thead><tr>\r\n';

    //Look for ordering field type
    for (let index = 0; index < l; index++) {
        let field = wizardFields[index];
        if (field.type == 'ordering') {
            result += '<th class="short">{{ ' + field.fieldname + '.label(true) }}</th>\r\n';
        }
    }

    result += '<th class="short">{{ html.batch("checkbox") }}</th>\r\n';
    result += '<th class="short">{{ record.label(true) }}</th>\r\n';

    for (let index = 0; index < l; index++) {
        result += renderTableColumnHeader(wizardFields[index], fieldtypes_to_skip, fieldTypesWithSearch, fieldtypes_allowed_to_orderby);
    }

    result += '<th>Action<br/>{{ html.searchbutton }}</th>\r\n';
    result += '</tr></thead>\r\n\r\n';

    return result;
}

function renderTableColumnHeader(field, fieldtypes_to_skip, fieldtypes_withsearch, fieldtypes_allowed_to_orderby) {

    let result = '';

    if (field.type != 'ordering' && fieldtypes_to_skip.indexOf(field.type) === -1) {

        result += '<th>';

        if (field.allowordering && fieldtypes_allowed_to_orderby.indexOf(field.type) !== -1)
            result += '{{ ' + field.fieldname + '.label(true) }}';
        else
            result += '{{ ' + field.fieldname + '.title }}';

        if (fieldtypes_withsearch.indexOf(field.type) !== -1) {

            if (field.type == 'checkbox' || field.type == 'sqljoin' || field.type == 'records')
                result += '<br/>{{ html.search("' + field.fieldname + '","","reload") }}';
            else
                result += '<br/>{{ html.search("' + field.fieldname + '") }}';
        }

        result += '</th>\r\n';
    }

    return result;
}

function getLayout_Edit() {
    let result = "";
    let l = wizardFields.length;

    result += '<legend>{{ table.title }}</legend>\r\n\r\n';
    result += '{{ html.goback("Go back") }}\r\n\r\n';
    result += '<div class="form-horizontal">';

    let fieldtypes_to_skip = ['log', 'phponview', 'phponchange', 'phponadd', 'md5', 'id', 'server', 'userid', 'viewcount', 'lastviewtime', 'changetime', 'creationtime', 'imagegallery', 'filebox', 'dummy'];

    for (let index = 0; index < l; index++) {
        let field = wizardFields[index];

        if (fieldtypes_to_skip.indexOf(field.type) === -1) {
            result += '\t<div class="control-group">\r\n';
            result += '\t\t<div class="control-label">{{ ' + field.fieldname + '.title }}</div><div class="controls">{{ ' + field.fieldname + '.edit }}</div>\r\n';
            result += '\t</div>\r\n\r\n';
        }
    }

    result += '</div>\r\n';
    result += '\r\n';

    for (let index2 = 0; index2 < l; index2++) {
        let field2 = wizardFields[index2];

        if (field2.fieldtyue === "dummy") {
            result += '<p><span style="color: #FB1E3D; ">*</span> {{ ' + field2.fieldname + '.title }}</p>\r\n';
            break;
        }
    }
    result += '<div style="text-align:center;">{{ html.button("save") }} {{ html.button("saveandclose") }} {{ html.button("saveascopy") }} {{ html.button("cancel") }}</div>\r\n';
    return result;
}

function getLayout_Details() {
    let result = "";
    let l = wizardFields.length;

    result += '<legend>{{ table.title }}</legend>\r\n\r\n';
    result += '{{ html.goback("Go back") }}\r\n\r\n'
    result += '<div class="form-horizontal">\r\n\r\n';

    let fieldtypes_to_skip = ['log', 'imagegallery', 'filebox', 'dummy'];

    for (let index = 0; index < l; index++) {
        let field = wizardFields[index];

        if (fieldtypes_to_skip.indexOf(field.type) === -1) {
            result += '\t<div class="control-group">\r\n';
            result += '\t\t<div class="control-label">{{ ' + field.fieldname + '.title }}</div>';

            let fieldValue = '';
            if (field.type == 'url')
                fieldValue = '<a href="{{ ' + field.fieldname + ' }}" target="_blank">{{ ' + field.fieldname + ' }}</a>';
            else
                fieldValue = '{{ ' + field.fieldname + ' }}';

            result += '<div class="controls">' + fieldValue + '</div>\r\n';
            result += '\t</div>\r\n\r\n';
        }
    }
    result += '</div>\r\n';
    result += '\r\n';
    return result;
}

function getLayout_Email() {
    let result = "";
    let l = wizardFields.length;
    result += '<p>New form entry registered:</p>\r\n\r\n';

    let fieldtypes_to_skip = ['log', 'imagegallery', 'filebox', 'dummy'];

    for (let index = 0; index < l; index++) {
        let field = wizardFields[index];

        if (fieldtypes_to_skip.indexOf(field.type) === -1)
            result += '\t\t<p>{{ ' + field.fieldname + '.title }}: {{ ' + field.fieldname + ' }}</p>\r\n';
    }
    return result;
}

function getLayout_CSV() {
    let result = "";
    let l = wizardFields.length;

    let fieldtypes_to_skip = ['log', 'imagegallery', 'filebox', 'dummy', 'ordering'];
    let fieldtypes_to_purevalue = ['image', 'imagegallery', 'filebox', 'file'];

    for (let index = 0; index < l; index++) {
        let field = wizardFields[index];

        if (fieldtypes_to_skip.indexOf(field.type) === -1) {
            if (result !== '')
                result += ',';

            result += '"{{ ' + field.fieldname + '.title }}"';
        }
    }

    result += '\r\n{% block record %}';

    let firstfield = true;
    for (let index = 0; index < l; index++) {
        let field = wizardFields[index];

        if (fieldtypes_to_skip.indexOf(field.type) === -1) {

            if (!firstfield)
                result += ',';

            if (fieldtypes_to_purevalue.indexOf(field.type) === -1)
                result += '"{{ ' + field.fieldname + ' }}"';
            else
                result += '"{{ ' + field.fieldname + '.value }}"';

            firstfield = false;
        }
    }
    result += '\r\n{% endblock %}';
    return result;
}

function getLayout_JSON() {
    let result = "";
    let l = wizardFields.length;

    result += '[\r\n{% block record %}\r\n{';
    result += '"id_":"{{ record.id }}",\r\n';

    let fieldtypes_to_skip = ['log', 'imagegallery', 'filebox', 'dummy', 'ordering'];
    let fieldtypes_to_purevalue = ['image', 'imagegallery', 'filebox', 'file'];
    let firstfield = true;

    for (let index = 0; index < l; index++) {
        let field = wizardFields[index];

        if (fieldtypes_to_skip.indexOf(field.type) === -1) {

            if (!firstfield)
                result += ',\r\n';

            if (fieldtypes_to_purevalue.indexOf(field.type) === -1)
                result += '"' + field.fieldname + '":"{{ ' + field.fieldname + ' }}"';
            else
                result += '"' + field.fieldname + '":"{{ ' + field.fieldname + '.value }}"';

            firstfield = false;
        }
    }
    result += '}{% if not record.islast %},{% endif %}\r\n{% endblock %}]\r\n';
    return result;
}

function getLayout_XML() {
    let result = "";
    let l = wizardFields.length;
    result += '<?xml version="1.0" encoding="utf-8"?>\r\n<document>\r\n{catalogtable:\r\n';
    let fieldtypes_to_skip = ['log', 'imagegallery', 'filebox', 'dummy', 'ordering'];
    for (let index = 0; index < l; index++) {
        let field = wizardFields[index];

        if (fieldtypes_to_skip.indexOf(field.type) === -1) {
            let v = '\t<field name=\'' + field.fieldname + '\' label=\'{{ ' + field.fieldname + '.title }}\'>{{ ' + field.fieldname + ' }}</field>\r\n';

            if (index === 0)
                result += '"":"<record id=\'{{ record.id }}\'>\r\n' + v + '"';
            else if (index === l - 1)
                result += '"":"' + v + '</record>\r\n"';
            else
                result += '"":"' + v + '"';

            if (index < l - 1)
                result += ',';
            else
                result += ';';
        }
    }
    result += '}\r\n</document>';
    return result;
}

function getLayout_Record() {
    let result = "";
    let l = wizardFields.length;
    let fieldtypes_to_skip = ['log', 'dummy'];
    let fieldtypes_to_purevalue = ['image', 'imagegallery', 'filebox', 'file', 'ordering'];

    for (let index = 0; index < l; index++) {
        let field = wizardFields[index];
        if (fieldtypes_to_skip.indexOf(field.type) === -1) {
            if (fieldtypes_to_purevalue.indexOf(field.type) === -1)
                result += '\t<div>{{ ' + field.fieldname + ' }}</div>\r\n';
            else
                result += '\t<div>{{ ' + field.fieldname + '.value }}</div>\r\n';
        }
    }
    return result;
}

function getActiveEditor() {
    let cm;
    if (typeof wp !== 'undefined')
        cm = codemirror_editors[codemirror_active_index].codemirror;
    else if (typeof Joomla !== 'undefined')
        cm = codemirror_editors[codemirror_active_index];

    return cm;
}