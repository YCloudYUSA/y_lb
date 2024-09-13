(($, Drupal) => {
  const originalDialogAttach = Drupal.AjaxCommands.prototype.openDialog;
  // Override Drupal.AjaxCommands.prototype.openDialog
  Drupal.AjaxCommands.prototype.openDialog = function (ajax, response, status) {
    originalDialogAttach(ajax, response, status);
    window.dispatchEvent(new Event('resize'));
  };

})(jQuery, Drupal);
