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
use Joomla\CMS\Language\Text;
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
                $listId = $jinput->getInt('listid');

                //Get Theme
                $mobileThemeId = $jinput->getInt('mobilethemeid');
                if ($mobileThemeId != 0) {
                    if (Environment::check_user_agent('mobile'))
                        $themeId = $mobileThemeId;
                    else
                        $themeId = $jinput->getInt('themeid');
                } else
                    $themeId = $jinput->getInt('themeid');
            } else {
                $listId = (int)$this->params->get('listid');
                //Get Theme
                $mobileThemeId = (int)$this->params->get('mobilethemeid');
                if ($mobileThemeId != 0) {
                    if (Environment::check_user_agent('mobile'))
                        $themeId = $mobileThemeId;
                    else
                        $themeId = (int)$this->params->get('themeid');
                } else
                    $themeId = (int)$this->params->get('themeid');
            }

            if ($listId == 0 and $themeId != 0) {
                if ($jinput->getInt('yg_api') == 1) {
                    $response = array('error' => Text::_('COM_YOUTUBEGALLERY_ERROR_VIDEOLIST_NOT_SET'));
                    echo json_encode($response);
                    die;
                } else {
                    Factory::getApplication()->enqueueMessage(Text::_('COM_YOUTUBEGALLERY_ERROR_VIDEOLIST_NOT_SET'), 'error');
                    return '';
                }
            } elseif ($themeId == 0 and $listId != 0) {
                if ($jinput->getInt('yg_api') == 1) {
                    $response = array('error' => Text::_('COM_YOUTUBEGALLERY_ERROR_THEME_NOT_SET'));
                    echo json_encode($response);
                    die;
                } else {
                    Factory::getApplication()->enqueueMessage(Text::_('COM_YOUTUBEGALLERY_ERROR_THEME_NOT_SET'), 'error');
                    return '';
                }
            } elseif ($themeId == 0 and $listId == 0) {
                if ($jinput->getInt('yg_api') == 1) {
                    $response = array('error' => Text::_('COM_YOUTUBEGALLERY_ERROR_VIDEOLIST_AND_THEME_NOT_SET'));
                    echo json_encode($response);
                    die;
                } else {
                    Factory::getApplication()->enqueueMessage(Text::_('COM_YOUTUBEGALLERY_ERROR_VIDEOLIST_AND_THEME_NOT_SET'), 'error');
                    return '';
                }
            }

            $videoId = $jinput->getCmd('videoid', '');
            $ygDB = new YouTubeGalleryDB;

            if (!$ygDB->getVideoListTableRow($listId))
                return '<p>No video found</p>';

            if (!$ygDB->getThemeTableRow($themeId))
                return '<p>No theme found</p>';

            if ($ygDB->theme_row->es_playvideo == 1 and $videoId != '')
                $ygDB->theme_row->es_autoplay = 1;

            $renderer = new YouTubeGalleryRenderer;
            $total_number_of_rows = 0;
            $ygDB->update_playlist();
            $videoIdNew = $videoId;
            $videoList = $ygDB->getVideoList_FromCache_From_Table($videoIdNew, $total_number_of_rows);

            if ($jinput->getInt('yg_api') == 1) {
                $videoList = Helper::prepareDescriptions($videoList);

                if (ob_get_contents())
                    ob_end_clean();

                //header('Content-Disposition: attachment; filename="youtubegallery_api.json"');
                //header('Content-Type: application/json; charset=utf-8');
                //header("Pragma: no-cache");
                //header("Expires: 0");

                echo json_encode($videoList);
                die;

                return '';
            }

            if ($videoId == '') {
                if ($ygDB->theme_row->es_playvideo == 1 and $videoIdNew != '') {
                    Factory::getApplication()->input->set('videoid', $videoIdNew);
                    $videoId = $videoIdNew;
                }
            }

            $galleryModule = $renderer->render(
                $videoList,
                $ygDB->videoListRow,
                $ygDB->theme_row,
                $total_number_of_rows,
                $videoId
            );

            $align = $this->params->get('align');

            switch ($align) {
                case 'left' :
                    $this->youtubegallerycode = '<div style="float:left;">' . $galleryModule . '</div>';
                    break;

                case 'center' :
                    if (((int)$ygDB->theme_row->es_width) > 0)
                        $this->youtubegallerycode = '<div style="width:' . $ygDB->theme_row->es_width . 'px;margin: 0 auto;">' . $galleryModule . '</div>';
                    else
                        $this->youtubegallerycode = $galleryModule;
                    break;

                case 'right' :
                    $this->youtubegallerycode = '<div style="float:right;">' . $galleryModule . '</div>';
                    break;

                default :
                    $this->youtubegallerycode = $galleryModule;
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
                $dispatcher->trigger('onContentPrepare', array('com_content.article', $o, $this->params_, 0));
            } else {
                Factory::getApplication()->triggerEvent('onContentPrepare', array('com_content.article', $o, $this->params_, 0));
            }
            $this->youtubegallerycode = $o->text;
        }

        $result .= $this->youtubegallerycode;
        return $result;
    }
}
