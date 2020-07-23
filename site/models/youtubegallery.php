<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev< <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
jimport('joomla.application.menu' );

/**
 * YoutubeGallery Model
 */
class YoutubeGalleryModelYoutubeGallery extends JModelItem
{
        protected $youtubegallerycode;
       
        public function getYoutubeGalleryCode()
        {
                require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_youtubegallery'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'db.php');
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_youtubegallery'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'render.php');
		$jinput=JFactory::getApplication()->input;
		$result='';

		$app	= JFactory::getApplication();
		$params	= $app->getParams();

				if (!isset($this->youtubegallerycode))
                {
						if($jinput->getInt('listid'))
						{
								//Shadow Box
								$listid=(int)$jinput->getInt('listid');


								//Get Theme
								$m_themeid=(int)JFactory::getApplication()->input->getInt('mobilethemeid');
								if($m_themeid!=0)
								{
									if(YouTubeGalleryMisc::check_user_agent('mobile'))
										$themeid=$m_themeid;
									else
										$themeid=(int)JFactory::getApplication()->input->getInt('themeid');
								}
								else
									$themeid=(int)JFactory::getApplication()->input->getInt('themeid');
						}
						else
						{
								$listid=(int)$params->get( 'listid' );
								//Get Theme
								$m_themeid=(int)$params->get( 'mobilethemeid' );
								if($m_themeid!=0)
								{
									if(YouTubeGalleryMisc::check_user_agent('mobile'))
										$themeid=$m_themeid;
									else
										$themeid=(int)$params->get( 'themeid' );
								}
								else
									$themeid=(int)$params->get( 'themeid' );
						}


                        if($listid==0 and $themeid!=0)
                        {
                                JFactory::getApplication()->enqueueMessage(JText::_( 'COM_YOUTUBEGALLERY_ERROR_VIDEOLIST_NOT_SET' ), 'error');
                                return '';
                        }
						elseif($themeid==0 and $listid!=0)
                        {
                                JFactory::getApplication()->enqueueMessage(JText::_( 'COM_YOUTUBEGALLERY_ERROR_THEME_NOT_SET' ), 'error');
                                return '';
                        }
                        elseif($themeid==0 and $listid==0)
                        {
                                JFactory::getApplication()->enqueueMessage(JText::_( 'COM_YOUTUBEGALLERY_ERROR_VIDEOLIST_AND_THEME_NOT_SET' ), 'error');
                                return '';
                        }



								$videoid=JFactory::getApplication()->input->getCmd('videoid');

								if($ygDB->theme_row->playvideo==1 and $videoid!='')
									$ygDB->theme_row->autoplay=1;
								

								$ygDB=new YouTubeGalleryDB;

								if(!$ygDB->getVideoListTableRow($listid))
										return '<p>No video found</p>';

								if(!$ygDB->getThemeTableRow($themeid))
										return  '<p>No video found</p>';

								$renderer= new YouTubeGalleryRenderer;


                                $total_number_of_rows=0;


								$ygDB->update_playlist();



								

                                $videoid_new=$videoid;
                                if($jinput->getInt('yg_api')==1)
                                {
                                        $videolist=$ygDB->getVideoList_FromCache_From_Table($videoid_new,$total_number_of_rows,false);
                                        $result=json_encode($videolist);

                                        if (ob_get_contents())
                                        	ob_end_clean();

                                        header('Content-Disposition: attachment; filename="youtubegallery_api.json"');
                                        header('Content-Type: application/json; charset=utf-8');
                                        header("Pragma: no-cache");
                                        header("Expires: 0");

                                        echo $result;
                                        die;

                                        return '';
                                }
                                else
                                {
                                        $videolist=$ygDB->getVideoList_FromCache_From_Table($videoid_new,$total_number_of_rows,false);
                                }

								if($videoid=='')
								{
									if($videoid_new!='')
										JFactory::getApplication()->input->setVar('videoid',$videoid_new);

									if($ygDB->theme_row->playvideo==1 and $videoid_new!='')
										$videoid=$videoid_new;
								}

        			$gallerymodule=$renderer->render(
										$videolist,
										$ygDB->videolist_row,
										$ygDB->theme_row,
										$total_number_of_rows,
										$videoid
				);


                                $align=$params->get( 'align' );


                                switch($align)
                                {
                                	case 'left' :
                                		$this->youtubegallerycode = '<div style="float:left;">'.$gallerymodule.'</div>';
                                		break;

                                	case 'center' :
										if(((int)$ygDB->theme_row->width)>0)
												$this->youtubegallerycode = '<div style="width:'.$ygDB->theme_row->width.'px;margin: 0 auto;">'.$gallerymodule.'</div>';
										else
												$this->youtubegallerycode = $gallerymodule;

                                		break;

                                	case 'right' :
                                		$this->youtubegallerycode = '<div style="float:right;">'.$gallerymodule.'</div>';
                                		break;

                                	default :
                                		$this->youtubegallerycode = $gallerymodule;
                                		break;

                                }






                }



				if($params->get( 'allowcontentplugins' ))
				{
								$o = new stdClass();
								$o->text=$this->youtubegallerycode;

								$dispatcher	= JDispatcher::getInstance();

								JPluginHelper::importPlugin('content');

								$r = $dispatcher->trigger('onContentPrepare', array ('com_content.article', &$o, &$params_, 0));

								$this->youtubegallerycode=$o->text;
				}

				$result.=$this->youtubegallerycode;


                return $result;
        }
}
