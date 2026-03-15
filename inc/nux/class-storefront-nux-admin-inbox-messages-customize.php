<?php
/**
 * Restroom NUX Admin Inbox Messages.
 *
 * @package  restroom
 * @since    3.0.0
 */

use Automattic\PooCommerce\Admin\Notes\Note;
use Automattic\PooCommerce\Admin\Notes\NoteTraits;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Restroom_NUX_Admin_Inbox_Messages_Customize' ) ) :
	/**
	 * The initial Restroom inbox Message.
	 */
	class Restroom_NUX_Admin_Inbox_Messages_Customize {

		use NoteTraits;

		/**
		 * Name of the note for use in the database.
		 */
		const NOTE_NAME = 'restroom-customize';

		/**
		 * Get the note.
		 *
		 * @return Note
		 */
		public static function get_note() {
			$note = new Note();
			$note->set_title( __( 'Design your store with Restroom 🎨', 'restroom' ) );
			$note->set_content( __( 'Visit the Restroom settings page to start setup and customization of your shop.', 'restroom' ) );
			$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
			$note->set_name( self::NOTE_NAME );
			$note->set_content_data( (object) array() );
			$note->set_source( 'restroom' );
			$note->add_action(
				'customize-store-with-restroom',
				__( 'Let\'s go!', 'restroom' ),
				admin_url( 'themes.php?page=restroom-welcome' ),
				Note::E_WC_ADMIN_NOTE_ACTIONED,
				true
			);
			return $note;
		}
	}
endif;
