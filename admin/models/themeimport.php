<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev< <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modellist');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder' );
jimport('joomla.filesystem.archive' );

/**
 * YoutubeGallery - Theme Import Model
 */
class YoutubeGalleryModelThemeImport extends JModelList
{
		function upload_theme(&$msg)
        {
				$jinput=JFactory::getApplication()->input;
				$file = $_FILES['themefile'];

				if(!isset($file['name']))
				{
						$msg='No file has bee uploaded.';
						return false; //wrong file format, expecting .zip
				}

				$uploadedfile= basename( $file['name']);
				echo 'Uploaded file: "'.$uploadedfile.'"<br/>';


				$folder_name=$this->getFolderNameOnly($file['name']);
				if($folder_name=='')
				{
						$msg='Wrong file format, expecting ".zip"';
						return false; //wrong file format, expecting .zip
				}


				$this->prepareFolderYG();
				$path=JPATH_SITE.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'youtubegallery'.DIRECTORY_SEPARATOR;

				if(file_exists($path.$uploadedfile))
				{
						echo 'Existing "'.$uploadedfile.'" file deleted.<br/>';
						unlink($path.$uploadedfile);
				}


				if(!move_uploaded_file($file['tmp_name'], $path.$uploadedfile))
				{
						$msg='Cannot Move File';

						return false;
				}

				echo 'File "'.$uploadedfile.'" moved form temporary location.<br/>';



				$folder_name_created=$this->prepareFolder($folder_name,$path);
				echo 'Folder "tmp'.DIRECTORY_SEPARATOR.'youtubegallery'.DIRECTORY_SEPARATOR.$folder_name_created.'" created.<br/>';

				//echo '$folder_name='.$folder_name.'<br/>';


				$zip =JArchive::getAdapter('zip');

				$zip->extract($path.$uploadedfile, $path.$folder_name_created);
				echo 'File "'.$uploadedfile.'" extracted.<br/>';

				unlink($path.$uploadedfile);
				echo 'File "'.$uploadedfile.'" deleted.<br/>';


				if(file_exists($path.$folder_name_created.DIRECTORY_SEPARATOR.'theme.txt'))
				{
					//Ok archive is fine, looks like it is really YG theme.
					$filedata=file_get_contents ($path.$folder_name_created.DIRECTORY_SEPARATOR.'theme.txt');
					if($filedata=='')
					{
						//Archive doesn't containe Gallery Data
						$msg='Gallery Data file is empty';

						JFolder::delete($path.'youtubegallery');
						return false;
					}

					$theme_row=unserialize($filedata);
						
					$theme_row->themename=$this->getThemeName(str_replace('"','',$theme_row->themename),true);//force to install
					if($theme_row->themename!='')
					{
						$theme_row->themedescription=file_get_contents ($path.$folder_name_created.DIRECTORY_SEPARATOR.'about.txt');

						echo 'Theme Data Found<br/>';

						if($theme_row->mediafolder!='')
						{
								//prepare media folder
								$theme_row->mediafolder=$this->prepareFolder($theme_row->mediafolder,JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR);
								echo 'Media Folder "'.$theme_row->mediafolder.'" created.<br/>';

								//move files
								$this->moveFiles(
									JPATH_SITE.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'youtubegallery'.DIRECTORY_SEPARATOR.$folder_name_created,
									JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$theme_row->mediafolder);
						}
						
						
						JFolder::delete($path);

						echo 'Theme Name: '.$theme_row->themename.'<br/>';

						$this->saveTheme($theme_row);
						echo 'Theme Imported<br/>';
					}
				}
				else
				{
					$msg="Archive doesn't containe Gallery Data";
					return false;
				}

				return true;
		}

		function createTheme($themecode, &$msg)
		{


				$theme_row=unserialize($themecode);
				if($theme_row===false)
				{

						$msg='Theme Code is corrupted.';
						return false;
				}


				if($theme_row->themename=='')
				{

						$msg= 'Theme Code is incorrect.';
						return false;
				}

				//Add record to database
				$theme_row->themename=$this->getThemeName(str_replace('"','',$theme_row->themename));
				echo 'Theme Name: '.$theme_row->themename.'<br/>';


				$this->saveTheme($theme_row);
				echo 'Theme Imported<br/>';

				return true;
		}


		function saveTheme(&$theme_row)
		{
				$fields=array();
				$db = JFactory::getDBO();
				$fields[]=$db->quoteName('themename').'='.$db->quote($theme_row->themename);
				$fields[]=$db->quoteName('width').'='.$db->quote($theme_row->width);
				$fields[]=$db->quoteName('height').'='.$db->quote($theme_row->height);
				$fields[]=$db->quoteName('playvideo').'='.$db->quote($theme_row->playvideo);
				$fields[]=$db->quoteName('repeat').'='.$db->quote($theme_row->repeat);
				$fields[]=$db->quoteName('fullscreen').'='.$db->quote($theme_row->fullscreen);
				$fields[]=$db->quoteName('autoplay').'='.$db->quote($theme_row->autoplay);
				$fields[]=$db->quoteName('related').'='.$db->quote($theme_row->related);
				$fields[]=$db->quoteName('showinfo').'='.$db->quote($theme_row->showinfo);
				$fields[]=$db->quoteName('bgcolor').'='.$db->quote($theme_row->bgcolor);
				$fields[]=$db->quoteName('cols').'='.$db->quote($theme_row->cols);
				$fields[]=$db->quoteName('showtitle').'='.$db->quote($theme_row->showtitle);
				$fields[]=$db->quoteName('cssstyle').'='.$db->quote($theme_row->cssstyle);
				$fields[]=$db->quoteName('navbarstyle').'='.$db->quote($theme_row->navbarstyle);
				$fields[]=$db->quoteName('thumbnailstyle').'='.$db->quote($theme_row->thumbnailstyle);
				$fields[]=$db->quoteName('linestyle').'='.$db->quote($theme_row->linestyle);
				$fields[]=$db->quoteName('showlistname').'='.$db->quote($theme_row->showlistname);
				$fields[]=$db->quoteName('listnamestyle').'='.$db->quote($theme_row->listnamestyle);
				$fields[]=$db->quoteName('showactivevideotitle').'='.$db->quote($theme_row->showactivevideotitle);
				$fields[]=$db->quoteName('activevideotitlestyle').'='.$db->quote($theme_row->activevideotitlestyle);
				$fields[]=$db->quoteName('description').'='.$db->quote($theme_row->description);
				$fields[]=$db->quoteName('descr_position').'='.$db->quote($theme_row->descr_position);
				$fields[]=$db->quoteName('descr_style').'='.$db->quote($theme_row->descr_style);
				$fields[]=$db->quoteName('color1').'='.$db->quote($theme_row->color1);
				$fields[]=$db->quoteName('color2').'='.$db->quote($theme_row->color2);
				$fields[]=$db->quoteName('border').'='.$db->quote($theme_row->border);
				$fields[]=$db->quoteName('openinnewwindow').'='.$db->quote($theme_row->openinnewwindow);
				$fields[]=$db->quoteName('rel').'='.$db->quote($theme_row->rel);
				$fields[]=$db->quoteName('hrefaddon').'='.$db->quote($theme_row->hrefaddon);
				$fields[]=$db->quoteName('pagination').'='.$db->quote($theme_row->pagination);
				$fields[]=$db->quoteName('customlimit').'='.$db->quote($theme_row->customlimit);
				$fields[]=$db->quoteName('controls').'='.$db->quote($theme_row->controls);
				$fields[]=$db->quoteName('youtubeparams').'='.$db->quote($theme_row->youtubeparams);
				$fields[]=$db->quoteName('playertype').'='.$db->quote($theme_row->playertype);
				$fields[]=$db->quoteName('useglass').'='.$db->quote($theme_row->useglass);
				$fields[]=$db->quoteName('logocover').'='.$db->quote($theme_row->logocover);
				$fields[]=$db->quoteName('customlayout').'='.$db->quote($theme_row->customlayout);

				$fields[]=$db->quoteName('prepareheadtags').'='.$db->quote($theme_row->prepareheadtags);
				$fields[]=$db->quoteName('muteonplay').'='.$db->quote($theme_row->muteonplay);
				$fields[]=$db->quoteName('volume').'='.$db->quote($theme_row->volume);
				$fields[]=$db->quoteName('orderby').'='.$db->quote($theme_row->orderby);
				$fields[]=$db->quoteName('customnavlayout').'='.$db->quote($theme_row->customnavlayout);
				$fields[]=$db->quoteName('responsive').'='.$db->quote($theme_row->responsive);
				$fields[]=$db->quoteName('mediafolder').'='.$db->quote($theme_row->mediafolder);
				$fields[]=$db->quoteName('readonly').'='.$db->quote($theme_row->readonly);
				$fields[]=$db->quoteName('headscript').'='.$db->quote($theme_row->headscript);
				$fields[]=$db->quoteName('themedescription').'='.$db->quote($theme_row->themedescription);

				if(isset($theme_row->nocookie))
						$fields[]=$db->quoteName('nocookie').'='.$db->quote($theme_row->nocookie);

				if(isset($theme_row->changepagetitle))
						$fields[]=$db->quoteName('changepagetitle').'='.$db->quote($theme_row->changepagetitle);

				$query='INSERT #__youtubegallery_themes SET '.implode(', ',$fields);

				$db = JFactory::getDBO();

				$db->setQuery($query);
				if (!$db->query())    die ( $db->stderr());
		}

		function getThemeName($themename,$force=false)
		{
			if(!$this->checkIfThemenameExist($themename))
				return $themename;//New theme / all good

			//Theme already exists
			
			if(!$force and ($themename=='Default' or $themename=='SimpleGridJCEPopup'))
			{
				//Do not update default themes
				return '';
			}

			$i=0;
			do
			{
				$i++;
			}while($this->checkIfThemenameExist($themename.' ('.$i.')'));

			return $themename.' ('.$i.')';
		}

		function checkIfThemenameExist($themename)
		{
			$db = JFactory::getDBO();

			$query = 'SELECT id FROM #__youtubegallery_themes WHERE '.$db->quoteName('themename').'='.$db->quote($themename).' LIMIT 1';
			$db->setQuery($query);
			if (!$db->query())    die ( $db->stderr());

			return $db->getNumRows()>0;
		}

        function moveFiles($dirpath_from,$dirpath_to)
        {
                $files_to_archive=array();

				
                //$sys_path=JPATH_SITE.DIRECTORY_SEPARATOR.$dirpath_from;
				$sys_path=$dirpath_from;
                if(file_exists($sys_path)===false)
                {
                        echo '<p>Media Folder "'.$dirpath_from.' ('.$sys_path.')" not found.</p>';
                        return $files_to_archive;
                }

                if ($handle = opendir($sys_path)) {

				while (false !== ($file = readdir($handle))) {

                        if($file!='.' and $file!='..' and $file!='theme.txt' and $file!='about.txt' and strpos($file,'.xml')===false and strpos($file,'.php')===false)
                        {
                                if(!is_dir($sys_path.DIRECTORY_SEPARATOR.$file))
                                {
                                        //$destination_file=JPATH_SITE.DIRECTORY_SEPARATOR.$dirpath_to.DIRECTORY_SEPARATOR.$file;
										$destination_file=$dirpath_to.DIRECTORY_SEPARATOR.$file;

										if(file_exists($sys_path.DIRECTORY_SEPARATOR.$file)===false)
										{
												echo '<span style="color:red;">file "'.$file.'" ('.$sys_path.DIRECTORY_SEPARATOR.$file.') not found.</span><br/>';
										}
										else
										{
												if(!(file_exists($destination_file)===false))
														unlink($destination_file);

												if(rename($sys_path.DIRECTORY_SEPARATOR.$file,$destination_file)===false)
														echo '<span style="color:red;">file "'.$file.'" cannot be moved.</span><br/>';
												else
														echo 'File "'.$file.'" moved.<br/>';
										}
                                }
                        }


				}

			}
        }

		function getFolderNameOnly($filename)
		{
				$p=explode('.',$filename);

				if(count($p)<2)
						return '';

				if(strtolower($p[1])!='zip')
						return '';

				return $p[0];
		}

		function prepareFolderYG()
		{
				$path=JPATH_SITE.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;

				if(file_exists($path.'youtubegallery'))
				{
				        //JFolder::delete($path.'youtubegallery');
				}
				else
				{
				        echo 'Folder "tmp/youtubegallery" created.<br/>';
				        mkdir($path.'youtubegallery');
				}
		}

		function prepareFolder($folder_base_name, $path)
		{
				$this->prepareFolderYG();

				if(file_exists($path.$folder_base_name) or file_exists($path.$folder_base_name.'.zip'))
				{
						$i=0;
						do
						{
								$i++;
								$folder=$folder_base_name.'_'.$i;
						}while(file_exists($path.$folder) or file_exists($path.$folder.'.zip'));
				}
				else
				        $folder=$folder_base_name;

				if(mkdir($path.$folder)===false)
				{
				        echo '<p>Cannot create temporary folder in "tmp/"</p>';
				        return '';
				}

				return $folder;
		}
}
