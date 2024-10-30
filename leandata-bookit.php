<?php
/**
 * LeanData BookIt
 *
 * @package       LDBOOKIT
 * @author        LeanData
 * @license       gplv2
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   LeanData BookIt
 * Plugin URI:    https://leandata.com
 * Description:   LeanData's official WordPress plugin serves as a tool to help attach BookIt to your form in just a few steps.
 * Version:       1.0.0
 * Author:        LeanData
 * Author URI:    www.leandata.com
 * Text Domain:   leandata-bookit
 * Domain Path:   /languages
 * License:       GPLv2
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with LeanData BookIt. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Plugin name
define( 'LDBOOKIT_NAME', 'LeanData BookIt' );

// Plugin version
define( 'LDBOOKIT_VERSION',	'1.0.0' );

// Plugin Root File
define( 'LDBOOKIT_PLUGIN_FILE',	__FILE__ );

// Plugin base
define( 'LDBOOKIT_PLUGIN_BASE',	plugin_basename( LDBOOKIT_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'LDBOOKIT_PLUGIN_DIR',	plugin_dir_path( LDBOOKIT_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'LDBOOKIT_PLUGIN_URL',	plugin_dir_url( LDBOOKIT_PLUGIN_FILE ) );

/**
 * Load the main class for the core functionality
 */
require_once LDBOOKIT_PLUGIN_DIR . 'core/class-leandata-bookit.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  LeanData
 * @since   1.0.0
 * @return  object|LDBookIt_Main
 */
function LDBOOKIT() {
  return LDBookIt_Main::instance();
}

LDBOOKIT();