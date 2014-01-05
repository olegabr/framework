/**
		 * Save this <?php echo $objTable->ClassName  ?>

		 * @param bool $blnForceInsert
		 * @param bool $blnForceUpdate
<?php
	$returnType = 'void';
	foreach ($objArray = $objTable->ColumnArray as $objColumn)
		if ($objColumn->Identity) {
			$returnType = 'int';
			break;
		}
	print '		 * @return '.$returnType;

	$strCols = '';
	$strValues = '';
	$strColUpdates = '';
	foreach ($objTable->ColumnArray as $objColumn) {
		if ((!$objColumn->Identity) && (!$objColumn->Timestamp)) {
			if ($strCols) $strCols .= ",\n";
			if ($strValues) $strValues .= ",\n";
			if ($strColUpdates) $strColUpdates .= ",\n";
			$strCol = '							' . $strEscapeIdentifierBegin.$objColumn->Name.$strEscapeIdentifierEnd;
			$strCols .= $strCol;
			$strValue = '\' . $objDatabase->SqlVariable($this->'.$objColumn->VariableName.') . \'';
			$strValues .= '							' . $strValue;
			$strColUpdates .= $strCol .' = '.$strValue;
		}
	}
	if ($strValues) {
		$strCols = " (\n".$strCols."\n						)";
		$strValues = " VALUES (\n".$strValues."\n						)\n";
	} else {
		$strValues = " DEFAULT VALUES";
	}

	$strIds = '';
	foreach ($objTable->PrimaryKeyColumnArray as $objPkColumn) {
		if ($strIds) $strIds .= " AND \n";
		$strIds .= '							' . $strEscapeIdentifierBegin.$objPkColumn->Name.$strEscapeIdentifierEnd .
			' = \' . $objDatabase->SqlVariable($this->' . ($objPkColumn->Identity ? '' : '__')  . $objPkColumn->VariableName . ') . \'';
	}

?>

		 */
		public function Save($blnForceInsert = false, $blnForceUpdate = false) {
			// Get the Database Object for this Class
			$objDatabase = <?php echo $objTable->ClassName  ?>::GetDatabase();

			$mixToReturn = null;

			try {
				if ((!$this->__blnRestored) || ($blnForceInsert)) {
					// Perform an INSERT query
					$objDatabase->NonQuery('
						INSERT INTO <?php echo $strEscapeIdentifierBegin  ?><?php echo $objTable->Name  ?><?php echo $strEscapeIdentifierEnd  ?><?php echo $strCols; echo $strValues; ?>
					');

<?php
	foreach ($objArray = $objTable->PrimaryKeyColumnArray as $objColumn) {
		if ($objColumn->Identity) {
			print sprintf('					// Update Identity column and return its value
					$mixToReturn = $this->%s = $objDatabase->InsertId(\'%s\', \'%s\');',
					$objColumn->VariableName, $objTable->Name, $objColumn->Name);
		}
	}
?>

				} else {
					// Perform an UPDATE query

					// First checking for Optimistic Locking constraints (if applicable)
<?php foreach ($objTable->ColumnArray as $objColumn) { ?>
<?php if ($objColumn->Timestamp) { ?>
					if (!$blnForceUpdate) {
						// Perform the Optimistic Locking check
						$objResult = $objDatabase->Query('
							SELECT
								<?php echo $strEscapeIdentifierBegin  ?><?php echo $objColumn->Name  ?><?php echo $strEscapeIdentifierEnd  ?>

							FROM
								<?php echo $strEscapeIdentifierBegin  ?><?php echo $objTable->Name  ?><?php echo $strEscapeIdentifierEnd  ?>

							WHERE
<?php echo $strIds; ?>

						');

						$objRow = $objResult->FetchArray();
						if ($objRow[0] != $this-><?php echo $objColumn->VariableName  ?>)
							throw new QOptimisticLockingException('<?php echo $objTable->ClassName  ?>');
					}
<?php } ?>
<?php } ?>

					// Perform the UPDATE query
<?php if ($strColUpdates) { ?>
					$objDatabase->NonQuery('
						UPDATE
							<?php echo $strEscapeIdentifierBegin  ?><?php echo $objTable->Name  ?><?php echo $strEscapeIdentifierEnd  ?>

						SET
<?php echo $strColUpdates; ?>

						WHERE
<?php echo $strIds; ?>

					');
<?php } else { ?>
					// Nothing to update
<?php }?>
				}

				// Update __blnRestored and any Non-Identity PK Columns (if applicable)
				$this->__blnRestored = true;
<?php foreach ($objTable->PrimaryKeyColumnArray as $objColumn) { ?>
<?php if ((!$objColumn->Identity) && ($objColumn->PrimaryKey)) { ?>
				$this->__<?php echo $objColumn->VariableName  ?> = $this-><?php echo $objColumn->VariableName  ?>;
<?php } ?>
<?php } ?>

<?php foreach ($objTable->ColumnArray as $objColumn) { ?>
<?php if ($objColumn->Timestamp) { ?>
				// Update Local Timestamp
				$objResult = $objDatabase->Query('
					SELECT
						<?php echo $strEscapeIdentifierBegin  ?><?php echo $objColumn->Name  ?><?php echo $strEscapeIdentifierEnd  ?>

					FROM
						<?php echo $strEscapeIdentifierBegin  ?><?php echo $objTable->Name  ?><?php echo $strEscapeIdentifierEnd  ?>

					WHERE
<?php echo $strIds; ?>

				');

				$objRow = $objResult->FetchArray();
				$this-><?php echo $objColumn->VariableName  ?> = $objRow[0];
<?php } ?>
<?php } ?>

				// Update QDbSpecific fields
				$blnHasQDbSpecificFields = false;
<?php foreach ($objTable->ColumnArray as $objColumn) { ?>

				if (!$blnHasQDbSpecificFields && $this-><?php echo $objColumn->VariableName  ?> instanceof QDbSpecific) {
					$blnHasQDbSpecificFields = true;
				}
<?php } ?>
				if ($blnHasQDbSpecificFields) {
					$intIdx = 0;
					$objResult = $objDatabase->Query(
						'SELECT'
<?php foreach ($objTable->ColumnArray as $objColumn) { ?>

							. ((($this-><?php echo $objColumn->VariableName  ?> instanceof QDbSpecific) && (0 != $intIdx++)) ? ' ,' : '')
							. (($this-><?php echo $objColumn->VariableName  ?> instanceof QDbSpecific) ? '<?php echo $strEscapeIdentifierBegin  ?><?php echo $objColumn->Name  ?><?php echo $strEscapeIdentifierEnd  ?>' : '')
<?php } ?>

						. ' FROM
							<?php echo $strEscapeIdentifierBegin  ?><?php echo $objTable->Name  ?><?php echo $strEscapeIdentifierEnd  ?>

						WHERE
<?php echo $strIds; ?>

					');

					$objRow = $objResult->FetchArray();
					$intQDbSpecificColumnIndex = 0;
					$strColumnArray = array();
<?php foreach ($objTable->ColumnArray as $objColumn) { ?>

					if ($this-><?php echo $objColumn->VariableName  ?> instanceof QDbSpecific) {
						$strColumnArray['<?php echo $objColumn->VariableName  ?>'] = $objRow[$intQDbSpecificColumnIndex];
						$intQDbSpecificColumnIndex++;
					}
<?php } ?>

					$objDbRow = new QPostgreSqlDatabaseRow($strColumnArray);
<?php foreach ($objTable->ColumnArray as $objColumn) { ?>

					if ($this-><?php echo $objColumn->VariableName  ?> instanceof QDbSpecific) {
						$this-><?php echo $objColumn->VariableName  ?> = $objDbRow->GetColumn('<?php echo $objColumn->VariableName  ?>', '<?php echo $objColumn->DbType  ?>');
					}
<?php } ?>

				}


				// Clean up cache to refill it with new loaded values
				// Cache key dependencies are handled here as well.
				// 
				$this->DeleteCache();

				// Update __blnRestored to prevent exception from Reload.
				//$this->__blnRestored = true;
				
				// set cache for every unique index
				//$this->Reload();
<?php foreach ($objTable->IndexArray as $objIndex) { ?>
<?php if ($objIndex->Unique) { ?>

				{
<?php $objColumnArray = $objCodeGen->GetColumnArray($objTable, $objIndex->ColumnNameArray); ?>
<?php foreach($objColumnArray as $objColumn) { ?>

					$<?php echo $objColumn->VariableName  ?> = $this-><?php echo $objColumn->VariableName  ?>;
<?php } ?>

					if (
<?php $blnFirst = true; ?>
<?php foreach($objColumnArray as $objColumn) { ?>
<?php if ($blnFirst) { ?>
						null != $<?php echo $objColumn->VariableName  ?>
<?php $blnFirst = false; ?>
<?php } else { ?>

						&& null != $<?php echo $objColumn->VariableName  ?>
<?php } ?>
<?php } ?>

					) {
						$strCacheKey = self::CreateCacheKeyHelper(<?php echo $objCodeGen->NamedParameterListFromColumnArray($objColumnArray);  ?>);
						if ($strCacheKey) {
							QApplication::$objCacheProvider->Set($strCacheKey, $this);
						}
					}

				}
<?php } ?>
<?php } ?>

			} catch (QDatabaseExceptionBase $objExc) {
				$this_dump = print_r($this,true); 
				error_log( date("H:i:s d.m.Y"). "\n" . "File: \"" . __FILE__ . "\".\nLine: " . __LINE__ . ".\nFunction: \"" . __FUNCTION__ .
						"\".\nUnable to save ORM object. DB Error \"\n\n" . $objExc->__toString() . "\"\n\noccured with number $objExc->ErrorNumber for query:\n $objExc->Query.\n\n" .
						"Dump of ORM object:\n\n" . $this_dump . "\n\n", 3, ERROR_LOG_PATH . '/error.log'); 
				throw $objExc;
			} catch (QCallerException $objExc) {
				$this_dump = print_r($this,true); 
				error_log( date("H:i:s d.m.Y"). "\n" . "File: \"" . __FILE__ . "\".\nLine: " . __LINE__ . ".\nFunction: \"" . __FUNCTION__ .
						"\".\nUnable to save ORM object. Error \"\n\n" . $objExc->__toString() . "\"\n\noccured.\n\n" .
						"Dump of ORM object:\n\n" . $this_dump . "\n\n", 3, ERROR_LOG_PATH . '/error.log'); 
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Return
			return $mixToReturn;
		}
