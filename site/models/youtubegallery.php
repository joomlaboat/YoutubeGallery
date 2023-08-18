<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use CustomTables\Environment;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Version;
use YouTubeGallery\Helper;

/**
 * YoutubeGallery Model
 */
class YoutubeGalleryModelYoutubeGallery extends ListModel//JModelItem
{
    var $params;
    protected $youtubegallerycode;

    public function getYoutubeGalleryCode()
    {
        $jinput = Factory::getApplication()->input;
        $result = '';

        $app = Factory::getApplication();
        $this->params = $app->getParams();

        if (!isset($this->youtubegallerycode)) {
            if ($jinput->getInt('listid')) {
                //Shadow Box
                $listid = (int)$jinput->getInt('listid');


                //Get Theme
                $m_themeid = (int)$jinput->getInt('mobilethemeid');
                if ($m_themeid != 0) {
                    if (Environment::check_user_agent('mobile'))
                        $themeid = $m_themeid;
                    else
                        $themeid = (int)$jinput->getInt('themeid');
                } else
                    $themeid = (int)$jinput->getInt('themeid');
            } else {
                $listid = (int)$this->params->get('listid');
                //Get Theme
                $m_themeid = (int)$this->params->get('mobilethemeid');
                if ($m_themeid != 0) {
                    if (Environment::check_user_agent('mobile'))
                        $themeid = $m_themeid;
                    else
                        $themeid = (int)$this->params->get('themeid');
                } else
                    $themeid = (int)$this->params->get('themeid');
            }

            if ($listid == 0 and $themeid != 0) {
                if ($jinput->getInt('yg_api') == 1) {
                    $response = array('error' => JText::_('COM_YOUTUBEGALLERY_ERROR_VIDEOLIST_NOT_SET'));
                    echo json_encode($response);
                    die;
                } else {
                    Factory::getApplication()->enqueueMessage(JText::_('COM_YOUTUBEGALLERY_ERROR_VIDEOLIST_NOT_SET'), 'error');
                    return '';
                }
            } elseif ($themeid == 0 and $listid != 0) {
                if ($jinput->getInt('yg_api') == 1) {
                    $response = array('error' => JText::_('COM_YOUTUBEGALLERY_ERROR_THEME_NOT_SET'));
                    echo json_encode($response);
                    die;
                } else {
                    Factory::getApplication()->enqueueMessage(JText::_('COM_YOUTUBEGALLERY_ERROR_THEME_NOT_SET'), 'error');
                    return '';
                }
            } elseif ($themeid == 0 and $listid == 0) {
                if ($jinput->getInt('yg_api') == 1) {
                    $response = array('error' => JText::_('COM_YOUTUBEGALLERY_ERROR_VIDEOLIST_AND_THEME_NOT_SET'));
                    echo json_encode($response);
                    die;
                } else {
                    Factory::getApplication()->enqueueMessage(JText::_('COM_YOUTUBEGALLERY_ERROR_VIDEOLIST_AND_THEME_NOT_SET'), 'error');
                    return '';
                }
            }


            $videoId = $jinput->getCmd('videoid');
            $ygDB = new YouTubeGalleryDB;

            if (!$ygDB->getVideoListTableRow($listid))
                return '<p>No video found</p>';


            if (!$ygDB->getThemeTableRow($themeid))
                return '<p>No theme found</p>';

            if ($ygDB->theme_row->es_playvideo == 1 and $videoId != '')
                $ygDB->theme_row->es_autoplay = 1;


            $renderer = new YouTubeGalleryRenderer;

            $total_number_of_rows = 0;

            $ygDB->update_playlist();

            $videoIdNew = $videoId;
            if ($jinput->getInt('yg_api') == 1) {
                $videolist = $ygDB->getVideoList_FromCache_From_Table($videoIdNew, $total_number_of_rows, false);
                $videolist = Helper::prepareDescriptions($videolist);

                if (ob_get_contents())
                    ob_end_clean();

                //header('Content-Disposition: attachment; filename="youtubegallery_api.json"');
                //header('Content-Type: application/json; charset=utf-8');
                //header("Pragma: no-cache");
                //header("Expires: 0");

                echo json_encode($videolist);
                die;

                return '';
            } else {
                $videolist = $ygDB->getVideoList_FromCache_From_Table($videoIdNew, $total_number_of_rows, false);
            }

            if ($videoId == '') {
                if ($ygDB->theme_row->es_playvideo == 1 and $videoIdNew != '') {
                    Factory::getApplication()->input->set('videoid', $videoIdNew);
                    $videoId = $videoIdNew;
                }
            }

            $gallerymodule = $renderer->render(
                $videolist,
                $ygDB->videoListRow,
                $ygDB->theme_row,
                $total_number_of_rows,
                $videoId
            );


            $align = $this->params->get('align');


            switch ($align) {
                case 'left' :
                    $this->youtubegallerycode = '<div style="float:left;">' . $gallerymodule . '</div>';
                    break;

                case 'center' :
                    if (((int)$ygDB->theme_row->es_width) > 0)
                        $this->youtubegallerycode = '<div style="width:' . $ygDB->theme_row->es_width . 'px;margin: 0 auto;">' . $gallerymodule . '</div>';
                    else
                        $this->youtubegallerycode = $gallerymodule;

                    break;

                case 'right' :
                    $this->youtubegallerycode = '<div style="float:right;">' . $gallerymodule . '</div>';
                    break;

                default :
                    $this->youtubegallerycode = $gallerymodule;
                    break;

            }
        }

        if ($this->params->get('allowcontentplugins')) {
            $version = new Version;
            $this->version = (int)$version->getShortVersion();

            $o = new stdClass();
            $o->text = $this->youtubegallerycode;
            $o->created_by_alias = 0;

            if ($this->version < 4) {
                $dispatcher = JDispatcher::getInstance();
                JPluginHelper::importPlugin('content');
                $results = $dispatcher->trigger('onContentPrepare', array('com_content.article', &$o, &$this->params_, 0));
            } else {
                $results = Factory::getApplication()->triggerEvent('onContentPrepare', array('com_content.article', &$o, &$this->params_, 0));
            }
            $this->youtubegallerycode = $o->text;
        }

        $result .= $this->youtubegallerycode;


        return $result;
    }
}
