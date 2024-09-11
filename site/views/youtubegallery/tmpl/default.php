<?php
/**
 * YouTube Gallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

if ($this->Model->params->get('show_page_heading', 1)): ?>

    <div class="page-header<?php echo $this->htmlEscape($this->Model->params->get('pageclass_sfx'), 'UTF-8'); ?>">
        <h2 itemprop="headline"><?php echo $this->Model->params->get('page_title'); ?></h2>
    </div>

<?php endif;

echo $this->youtubegallerycode;
