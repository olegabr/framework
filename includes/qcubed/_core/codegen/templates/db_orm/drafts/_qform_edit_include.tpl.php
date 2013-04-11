<template OverwriteFlag="true" DocrootFlag="true" DirectorySuffix="" TargetDirectory="<?php echo __FORM_DRAFTS__  ?>" TargetFileName="<?php echo QConvertNotation::UnderscoreFromCamelCase($objTable->ClassName)  ?>_edit.tpl.php"/>
<?php print("<?php\n"); ?>
	// This is the HTML template include file (.tpl.php) for the <?php echo QConvertNotation::UnderscoreFromCamelCase($objTable->ClassName)  ?>_edit.php
	// form DRAFT page.  Remember that this is a DRAFT.  It is MEANT to be altered/modified.

	// Be sure to move this out of the generated/ subdirectory before modifying to ensure that subsequent
	// code re-generations do not overwrite your changes.

	$strPageTitle = QApplication::Translate('<?php echo $objTable->ClassName  ?>') . ' - ' . $this->mct<?php echo $objTable->ClassName  ?>->TitleVerb;
	require(__CONFIGURATION__ . '/header.inc.php');
?>
	<?php print("<?php"); ?> $this->RenderBegin() ?>

	<h1><?php print("<?php"); ?> _p($this->mct<?php echo $objTable->ClassName  ?>->TitleVerb); ?> <?php print("<?php"); ?> _t('<?php echo $objTable->ClassName  ?>')?></h1>

	<div class="form-controls">
<?php foreach ($objTable->ColumnArray as $objColumn) { ?>
		<?php print("<?php"); ?> $this-><?php echo $objCodeGen->FormControlVariableNameForColumn($objColumn);  ?>->RenderWithName(); ?>

<?php } ?>
<?php foreach ($objTable->ReverseReferenceArray as $objReverseReference) { ?>
<?php if ($objReverseReference->Unique) { ?>
		<?php print("<?php"); ?> $this-><?php echo $objCodeGen->FormControlVariableNameForUniqueReverseReference($objReverseReference);  ?>->RenderWithName(); ?>

<?php } ?>
<?php } ?>
<?php foreach ($objTable->ManyToManyReferenceArray as $objManyToManyReference) { ?>
		<?php print("<?php"); ?> $this-><?php echo $objCodeGen->FormControlVariableNameForManyToManyReference($objManyToManyReference);  ?>->RenderWithName(true, "Rows=7"); ?>

<?php } ?>
	</div>

	<div class="form-actions">
		<div class="form-save"><?php print("<?php"); ?> $this->btnSave->Render(); ?></div>
		<div class="form-cancel"><?php print("<?php"); ?> $this->btnCancel->Render(); ?></div>
		<div class="form-delete"><?php print("<?php"); ?> $this->btnDelete->Render(); ?></div>
	</div>

	<?php print("<?php"); ?> $this->RenderEnd() ?>

<?php print("<?php"); ?> require(__CONFIGURATION__ .'/footer.inc.php'); ?>