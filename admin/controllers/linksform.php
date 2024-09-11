<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/**
 * YoutubeGallery - LinksForm Controller
 */
class YoutubeGalleryControllerLinksForm extends FormController
{
    var string $ref;
    var int $refId;

    /**
     * Current or most recently performed task.
     *
     * @var    string
     * @since  12.2
     * @note   Replaces _task.
     */
    protected $task;

    /**
     * @throws Exception
     */
    public function __construct($config = array())
    {
        $this->view_list = 'linkslist'; // safeguard for setting the return view listing to the main view.
        parent::__construct($config);
    }

    /**
     * Method to cancel an edit.
     *
     * @param string $key The name of the primary key of the URL variable.
     *
     * @return  boolean  True if access level checks pass, false otherwise.
     *
     * @since   12.2
     */
    public function cancel($key = null): bool
    {
        // get the referral details
        $this->ref = $this->input->get('ref', 0, 'word');
        $this->refId = $this->input->get('refid', 0, 'int');

        $cancel = parent::cancel($key);

        if ($cancel) {
            if ($this->refId) {
                $redirect = '&view=' . $this->ref . '&layout=edit&id=' . (int)$this->refId;

                // Redirect to the item screen.
                $this->setRedirect(
                    Route::_(
                        'index.php?option=' . $this->option . $redirect, false
                    )
                );
            } elseif ($this->ref) {
                $redirect = '&view=' . $this->ref;

                // Redirect to the list screen.
                $this->setRedirect(
                    Route::_(
                        'index.php?option=' . $this->option . $redirect, false
                    )
                );
            }
        } else {
            // Redirect to the items screen.
            $this->setRedirect(
                Route::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list, false
                )
            );
        }
        return $cancel;
    }

    /**
     * Method to save a record.
     *
     * @param string $key The name of the primary key of the URL variable.
     * @param string $urlVar The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
     *
     * @return  boolean  True if successful, false otherwise.
     *
     * @since   12.2
     */
    public function save($key = null, $urlVar = null): bool
    {
        // get the referral details
        $this->refId = $this->input->getInt('id', 0);

        $model = $this->getModel();
        $data = $this->input->post->get('jform', array(), 'array');
        $saved = $model->saveVideoList($data);

        if ($this->task == 'apply')
            $redirect = '&view=linksform&layout=edit&id=' . $this->refId;
        else
            $redirect = '&view=linkslist';

        $tmpl = $this->input->getCmd('tmpl');
        if ($tmpl !== null)
            $redirect .= '&tmpl=' . $tmpl;

        $ygRefreshParent = $this->input->getInt('ygrefreshparent');
        if ($ygRefreshParent !== null)
            $redirect .= '&ygrefreshparent=' . $ygRefreshParent;

        // Redirect to the item screen.
        $url = Uri::root() . 'administrator/index.php?option=com_youtubegallery' . $redirect;
        $this->setRedirect($url);
        return $saved;
    }

    /**
     * Method override to check if you can add a new record.
     *
     * @param array $data An array of input data.
     *
     * @return  boolean
     *
     * @since   1.6
     */
    protected function allowAdd($data = array()): bool
    {
        // In the absence of better information, revert to the component permissions.
        return parent::allowAdd($data);
    }

    /**
     * Gets the URL arguments to append to an item redirect.
     *
     * @param integer $recordId The primary key id for the item.
     * @param string $urlVar The name of the URL variable for the id.
     *
     * @return  string  The arguments to append to the redirect URL.
     *
     * @since   12.2
     */
    protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id'): string
    {
        $tmpl = $this->input->get('tmpl');
        $layout = $this->input->get('layout', 'edit', 'string');

        $ref = $this->input->get('ref', 0, 'string');
        $refid = $this->input->get('refid', 0, 'int');

        // Setup redirect info.

        $append = '';

        if ($refid) {
            $append .= '&ref=' . $ref . '&refid=' . (int)$refid;
        } elseif ($ref) {
            $append .= '&ref=' . $ref;
        }

        if ($tmpl) {
            $append .= '&tmpl=' . $tmpl;
        }

        if ($layout) {
            $append .= '&layout=' . $layout;
        }

        if ($recordId) {
            $append .= '&' . $urlVar . '=' . $recordId;
        }

        return $append;
    }
}