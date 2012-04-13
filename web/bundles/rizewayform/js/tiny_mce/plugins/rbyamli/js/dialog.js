tinyMCEPopup.requireLangPack();

var RbYamliDialog = {
	init : function() {
	},

	insert : function() {
		// Insert the contents from the input into the document
		tinyMCEPopup.editor.execCommand('mceInsertContent', false, document.forms[0].yamlitext.value);
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(RbYamliDialog.init, RbYamliDialog);
