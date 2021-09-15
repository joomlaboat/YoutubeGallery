/**
 * Youtube Gallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

Joomla.submitbutton = function(task)
{
	if (task == ''){
		return false;
	} else { 
		var isValid=true;
		var action = task.split('.');
		if (action[1] != 'cancel' && action[1] != 'close'){
			
			let form = document.getElementById('adminForm');
			if (!document.formvalidator.isValid(form))
				isValid = false;
		}
		if (isValid){
			Joomla.submitform(task);
			return true;
		} else {
			//alert(Joomla.JText._('tables, some values are not acceptable.','Some values are unacceptable'));
			return false;
		}
	}
}
