		/**
		 * Create and setup QJqTextBox <?php echo $strControlId  ?>

		 * @param string $strControlId optional ControlId to use
		 * @return QJqTextBox
		 */
		public function <?php echo $strControlId  ?>_Create($strControlId = null) {
			$this-><?php echo $strControlId  ?> = new QJqTextBox($this->objParentObject, $strControlId);
			$this-><?php echo $strControlId  ?>->Name = QApplication::Translate('<?php echo QCodeGen::MetaControlLabelNameFromColumn($objColumn)  ?>');
			$this-><?php echo $strControlId ?>_Refresh();
<?php if ($objColumn->NotNull) { ?>
			$this-><?php echo $strControlId  ?>->Required = true;
<?php } ?>
<?php if ($objColumn->DbType == QDatabaseFieldType::Blob) { ?>
			$this-><?php echo $strControlId  ?>->TextMode = QTextMode::MultiLine;
<?php } ?>
<?php if (($objColumn->VariableType == QType::String) && (is_numeric($objColumn->Length))) { ?>
			$this-><?php echo $strControlId  ?>->MaxLength = <?php echo $strClassName  ?>::<?php echo $objColumn->PropertyName  ?>MaxLength;
<?php } ?>
			return $this-><?php echo $strControlId  ?>;
		}

		/**
		 * Refresh QJqTextBox <?php echo $strControlId ?>

		 * @return QJqTextBox
		 */
		public function <?php echo $strControlId ?>_Refresh() {
			$this-><?php echo $strControlId ?>->Text = $this-><?php echo $strObjectName ?>-><?php echo $objColumn->PropertyName ?>;
			return $this-><?php echo $strControlId ?>;
		}

		/**
		 * Update QJqTextBox <?php echo $strControlId ?>

		 * @return QJqTextBox
		 */
		public function <?php echo $strControlId ?>_Update() {
			$this-><?php echo $strObjectName ?>-><?php echo $objColumn->PropertyName ?> = $this-><?php echo $strControlId ?>->Text;
			return $this-><?php echo $strControlId ?>;
		}

		/**
		 * Create and setup QLabel <?php echo $strLabelId  ?>

		 * @param string $strControlId optional ControlId to use
		 * @return QLabel
		 */
		public function <?php echo $strLabelId  ?>_Create($strControlId = null) {
			$this-><?php echo $strLabelId  ?> = new QLabel($this->objParentObject, $strControlId);
			$this-><?php echo $strLabelId  ?>->Name = QApplication::Translate('<?php echo QCodeGen::MetaControlLabelNameFromColumn($objColumn)  ?>');
			$this-><?php echo $strLabelId  ?>->Text = $this-><?php echo $strObjectName  ?>-><?php echo $objColumn->PropertyName  ?>;
<?php if ($objColumn->NotNull) { ?>
			$this-><?php echo $strLabelId  ?>->Required = true;
<?php } ?>
			return $this-><?php echo $strLabelId  ?>;
		}