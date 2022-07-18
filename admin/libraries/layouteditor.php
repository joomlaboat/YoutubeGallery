<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Version;

$version_object = new Version;
$version = (int)$version_object->getShortVersion();

$theme='eclipse';
$document = Factory::getDocument();

$adminpath=JURI::root(true).'/administrator/components/com_youtubegallery/';
$document->addCustomTag('<script src="'.JURI::root().'components/com_youtubegallery/js/ajax.js"></script>');
$document->addCustomTag('<script src="'.JURI::root().'components/com_youtubegallery/js/typeparams.js"></script>');

if($version < 4)
	$document->addCustomTag('<script src="'.$adminpath.'js/layouteditor.js"></script>');
else
	$document->addCustomTag('<script src="'.$adminpath.'js/layouteditor_quatro.js"></script>');


$document->addCustomTag('<link href="'.$adminpath.'css/layouteditor.css" rel="stylesheet">');

$document->addCustomTag('<link rel="stylesheet" href="'.$adminpath.'libraries/codemirror/lib/codemirror.css">');
$document->addCustomTag('<link rel="stylesheet" href="'.$adminpath.'libraries/codemirror/addon/hint/show-hint.css">');

$document->addCustomTag('<script src="'.$adminpath.'libraries/codemirror/lib/codemirror.js"></script>');
$document->addCustomTag('<script src="'.$adminpath.'libraries/codemirror/addon/mode/overlay.js"></script>');

$document->addCustomTag('<script src="'.$adminpath.'libraries/codemirror/addon/hint/show-hint.js"></script>');
$document->addCustomTag('<script src="'.$adminpath.'libraries/codemirror/addon/hint/xml-hint.js"></script>');
$document->addCustomTag('<script src="'.$adminpath.'libraries/codemirror/addon/hint/html-hint.js"></script>');
$document->addCustomTag('<script src="'.$adminpath.'libraries/codemirror/mode/xml/xml.js"></script>');
$document->addCustomTag('<script src="'.$adminpath.'libraries/codemirror/mode/javascript/javascript.js"></script>');
$document->addCustomTag('<script src="'.$adminpath.'libraries/codemirror/mode/css/css.js"></script>');
$document->addCustomTag('<script src="'.$adminpath.'libraries/codemirror/mode/htmlmixed/htmlmixed.js"></script>');
$document->addCustomTag('<link rel="stylesheet" href="'.$adminpath.'libraries/codemirror/theme/'.$theme.'.css">');



		function renderEditor($textareacode,$layoutname,&$onPageLoads)
		{
			$index=count($onPageLoads);

							$result='
								<div class="layouteditorbox">'.$textareacode.'</div>
';

							$result.='<div id="tags_'.$layoutname.'" class="ygTagAreaTab">'.$layoutname.' tags</div>
';

			$code='';

			$code.='

		text_areas.push(["'.$layoutname.'",'.$index.']);
        codemirror_editors['.$index.'] = CodeMirror.fromTextArea(document.getElementById("jform_es_'.$layoutname.'"), {
          mode: "layouteditor",
	   lineNumbers: true,
        lineWrapping: true,
		theme: "eclipse",
          extraKeys: {"Ctrl-Space": "autocomplete"}

        });
	      var charWidth'.$index.' = codemirror_editors['.$index.'].defaultCharWidth(), basePadding = 4;
      codemirror_editors['.$index.'].on("renderLine", function(cm, line, elt) {
        var off = CodeMirror.countColumn(line.text, null, cm.getOption("tabSize")) * charWidth'.$index.';
        elt.style.textIndent = "-" + off + "px";
        elt.style.paddingLeft = (basePadding + off) + "px";
      });


		addExtraEvents('.$index.');

	';
			$onPageLoads[]=$code;

			return $result;
		}

	function render_onPageLoads($onPageLoads,$LayoutType)
	{

		$result='
		<div id="layouteditor_Modal" class="layouteditor_modal">

  <!-- Modal content -->
  <div class="layouteditor_modal-content" id="layouteditor_modalbox">
    <span class="layouteditor_close">&times;</span>
	<div id="layouteditor_modal_content_box">
    <p>Some text in the Modal..</p>
	</div>
  </div>

</div>
';

	$result_js='
	<script type="text/javascript">
	websiteroot="'.JURI::root(true).'";

	define_cmLayoutEditor();


	var text_areas=[];
    window.onload = function()
	{

		loadTagParams();

		//loadTypes_silent("ct_processMessageBox");

	'.implode('',$onPageLoads).'

	setTimeout(addTabExtraEvents, 10);

    };

    </script>';

	    $document = Factory::getDocument();
		$document->addCustomTag($result_js);

		return $result;

	}
