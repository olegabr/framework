<?php

	/**
	 * QForm
	 * 
	 * @package Controls
	 */
     
     
	/**
	 * QForm
	 * 
	 * @package Controls  
	 * @author Qcubed
	 * @copyright 
	 * @version 2011
	 * @access public
	 * QForm
	 * 
	 * @package Controls  
	 * @author Qcubed
	 * @copyright 
	 * @version 2011
	 * @access public
	 */
	abstract class QForm extends QFormBase {
		///////////////////////////
		// Form Preferences
		///////////////////////////

		/**
		 * If you wish to encrypt the resulting formstate data to be put on the form (via
		 * QCryptography), please specify a key to use.  The default cipher and encrypt mode
		 * on QCryptography will be used, and because the resulting encrypted data will be
		 * sent via HTTP POST, it will be Base64 encoded.
		 *
		 * @var string EncryptionKey the key to use, or NULL if no encryption is required
		 */
		public static $EncryptionKey = null;

		/**
		 * The QFormStateHandler to use to handle the actual serialized form.  By default,
		 * QFormStateHandler will be used (which simply outputs the entire serialized
		 * form data stream to the form), but file- and session- based, or any custom db-
		 * based FormState handling can be used as well.
		 *
		 * @var string FormStateHandler the classname of the FormState handler to use
		 */
		public static $FormStateHandler = __FORM_STATE_HANDLER__;

		/**
		 * These are the list of core QForm JavaScript files, or JavaScript files needed by
		 * a QControl, which QForm should IGNORE trying to load during a RenderBegin() or RenderAjax() call.
		 * 
		 * @var array
		 */
		protected $strIgnoreJavaScriptFileArray = array();
		/* protected $strIgnoreJavaScriptFileArray = array(
			'date_time_picker.js',
			'treenav.js'); */

		/**
		 * This should be very rarely used.
		 * 
		 * This mechanism acts similarly to the strIgnoreJavascriptFileArray, except it applies to StyleSheets.
		 * However, any QControl that specifies a StyleSheet file to include is MEANT to have that property be modified / customized.
		 * 
		 * Therefore, there should be little to no need for this attribute.  However, it is here anyway, just in case.
		 *
		 * @var array
		 */
		protected $strIgnoreStyleSheetFileArray = array();
		// protected $strIgnoreStyleSheetFileArray = array('datagrid.css', 'calendar.css', 'textbox.css', 'listbox.css');
		
		
		protected function Form_Create() {
			parent::Form_Create();
		}
		
	}
?>
