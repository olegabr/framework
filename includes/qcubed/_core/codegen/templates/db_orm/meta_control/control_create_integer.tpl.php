		/**
		 * Create and setup QSpinner <?php echo $strControlId  ?>

		 * @param string $strControlId optional ControlId to use
		 * @return QSpinner
		 */
		public function <?php echo $strControlId  ?>_Create($strControlId = null) {
			$this-><?php echo $strControlId  ?> = new QSpinner($this->objParentObject, $strControlId);
			$this-><?php echo $strControlId  ?>->Name = QApplication::Translate('<?php echo QCodeGen::MetaControlLabelNameFromColumn($objColumn)  ?>');
			$this-><?php echo $strControlId ?>_Refresh();
<?php if ($objColumn->NotNull) { ?>
			$this-><?php echo $strControlId  ?>->Required = true;
<?php } ?>
			return $this-><?php echo $strControlId  ?>;
		}

		/**
		 * Refresh QSpinner <?php echo $strControlId ?>

		 * @return QSpinner
		 */
		public function <?php echo $strControlId ?>_Refresh() {
			$this-><?php echo $strControlId ?>->Text = $this-><?php echo $strObjectName ?>-><?php echo $objColumn->PropertyName ?>;
			return $this-><?php echo $strControlId ?>;
		}

		/**
		 * Reset QSpinner <?php echo $strControlId ?>

		 * @return QSpinner
		 */
		public function <?php echo $strControlId ?>_Reset() {
			$this-><?php echo $strControlId ?>->Text = null;
			return $this-><?php echo $strControlId ?>;
		}

		/**
		 * Make search query for QSpinner <?php echo $strControlId  ?> to be used in a search query.
		 * @return QQCondition
		 */
		public function <?php echo $strControlId  ?>_MakeSearchQuery() {
			if (null !== $this-><?php echo $strControlId  ?>->Text && strlen($this-><?php echo $strControlId  ?>->Text) > 0) {
				return QQ::Equal(QQN::<?php echo $objTable->ClassName  ?>()-><?php echo $objColumn->PropertyName  ?>, $this-><?php echo $strControlId  ?>->Text);
			}
			return null;
		}

		/**
		 * Update QSpinner <?php echo $strControlId ?>

		 * @return QSpinner
		 */
		public function <?php echo $strControlId ?>_Update() {
			$this-><?php echo $strObjectName ?>-><?php echo $objColumn->PropertyName ?> = $this-><?php echo $strControlId ?>->Text;
			return $this-><?php echo $strControlId ?>;
		}
		
		/**
		 * Create and setup QLabel <?php echo $strLabelId  ?>

		 * @param string $strControlId optional ControlId to use
		 * @param string $strFormat optional sprintf format to use
		 * @return QLabel
		 */
		public function <?php echo $strLabelId  ?>_Create($strControlId = null, $strFormat = null) {
			$this-><?php echo $strLabelId  ?> = new QLabel($this->objParentObject, $strControlId);
			$this-><?php echo $strLabelId  ?>->Name = QApplication::Translate('<?php echo QCodeGen::MetaControlLabelNameFromColumn($objColumn)  ?>');
			$this-><?php echo $strLabelId  ?>->Text = $this-><?php echo $strObjectName  ?>-><?php echo $objColumn->PropertyName  ?>;
<?php if ($objColumn->NotNull) { ?>
			$this-><?php echo $strLabelId  ?>->Required = true;
<?php } ?>
			$this-><?php echo $strLabelId  ?>->Format = $strFormat;
			return $this-><?php echo $strLabelId  ?>;
		}