<?php 
	// Preliminary calculations and helper routines here

	$blnImmediateExpansions = $objTable->HasImmediateArrayExpansions();
	$blnExtendedExpansions = $objTable->HasExtendedArrayExpansions($objCodeGen);

	if (count($objTable->PrimaryKeyColumnArray) > 1 &&
			$blnImmediateExpansions) {
		throw QCallerException ("Multi-key table with array expansion not supported.");
	}
	
		
?>///////////////////////////////
		// INSTANTIATION-RELATED METHODS
		///////////////////////////////

		/**
		 * Do a possible array expansion on the given node. If the node is an ExpandAsArray node,
		 * it will add to the corresponding array in the object. Otherwise, it will follow the node
		 * so that any leaf expansions can be handled.
		 *  
		 * @param DatabaseRowBase $objDbRow
		 * @param QQBaseNode $objChildNode
		 * @param QBaseClass $objPreviousItem
		 * @param string[] $strColumnAliasArray
		 */
		
		public static function ExpandArray ($objDbRow, $strAliasPrefix, $objNode, $objPreviousItemArray, $strColumnAliasArray) {
			if (!$objNode->ChildNodeArray) {
				return false;
			}
			
			$strAlias = $strAliasPrefix . '<?php echo $objTable->PrimaryKeyColumnArray[0]->Name  ?>';
			$strColumnAlias = !empty($strColumnAliasArray[$strAlias]) ? $strColumnAliasArray[$strAlias] : $strAlias;
			$blnExpanded = false;
			
			foreach ($objPreviousItemArray as $objPreviousItem) {
				if ($objPreviousItem-><?php echo $objTable->PrimaryKeyColumnArray[0]->VariableName  ?> != $objDbRow->GetColumn($strColumnAlias, '<?php echo $objTable->PrimaryKeyColumnArray[0]->DbType  ?>')) {
					continue;
				}
				
				foreach ($objNode->ChildNodeArray as $objChildNode) {	
					$strPropName = $objChildNode->_PropertyName;
					$strClassName = $objChildNode->_ClassName;
					$blnExpanded = false;
					$strLongAlias = $objChildNode->ExtendedAlias();
				
					if ($objChildNode->ExpandAsArray) {
						$strVarName = '_obj' . $strPropName . 'Array';
						if (null === $objPreviousItem->$strVarName) {
							$objPreviousItem->$strVarName = array();
						}
						if ($intPreviousChildItemCount = count($objPreviousItem->$strVarName)) {
							$objPreviousChildItems = $objPreviousItem->$strVarName;
							if ($objChildNode->_Type == "association") {
								$objChildNode = $objChildNode->FirstChild();
							}
							$nextAlias = $objChildNode->ExtendedAlias() . '__';
							
							$objChildItem = call_user_func(array ($strClassName, 'InstantiateDbRow'), $objDbRow, $nextAlias, $objChildNode, $objPreviousChildItems, $strColumnAliasArray);
							if ($objChildItem) {
								$objPreviousItem->{$strVarName}[] = $objChildItem;
								$blnExpanded = true;
							} elseif ($objChildItem === false) {
								$blnExpanded = true;
							}
						}
					} else {
	
						// Follow single node if keys match
						$nodeType = $objChildNode->_Type;
						if ($nodeType == 'reverse_reference' || $nodeType == 'association') {
							$strVarName = '_obj' . $strPropName;
						} else {	
							$strVarName = 'obj' . $strPropName;
						}
						
						if (null === $objPreviousItem->$strVarName) {
							return false;
						}
											
						$objPreviousChildItems = array($objPreviousItem->$strVarName);
						$blnResult = call_user_func(array ($strClassName, 'ExpandArray'), $objDbRow, $strLongAlias . '__', $objChildNode, $objPreviousChildItems, $strColumnAliasArray);
		
						if ($blnResult) {
							$blnExpanded = true;
						}		
					}
				}	
			}
			return $blnExpanded;
		}
		
		/**
		 * Instantiate a <?php echo $objTable->ClassName  ?> from a Database Row.
		 * Takes in an optional strAliasPrefix, used in case another Object::InstantiateDbRow
		 * is calling this <?php echo $objTable->ClassName  ?>::InstantiateDbRow in order to perform
		 * early binding on referenced objects.
		 * @param DatabaseRowBase $objDbRow
		 * @param string $strAliasPrefix
		 * @param QQBaseNode $objExpandAsArrayNode
		 * @param QBaseClass $arrPreviousItem
		 * @param string[] $strColumnAliasArray
		 * @return mixed Either a <?php echo $objTable->ClassName  ?>, or false to indicate the dbrow was used in an expansion, or null to indicate that this leaf is a duplicate.
		*/
		public static function InstantiateDbRow($objDbRow, $strAliasPrefix = null, $objExpandAsArrayNode = null, $objPreviousItemArray = null, $strColumnAliasArray = array()) {
			// If blank row, return null
			if (!$objDbRow) {
				return null;
			}
			
<?php if ($objTable->PrimaryKeyColumnArray)  { // Optimize top level accesses?>
			if (empty ($strAliasPrefix) && $objPreviousItemArray) {
				$strColumnAlias = !empty($strColumnAliasArray['<?php echo $objTable->PrimaryKeyColumnArray[0]->Name  ?>']) ? $strColumnAliasArray['<?php echo $objTable->PrimaryKeyColumnArray[0]->Name  ?>'] : '<?php echo $objTable->PrimaryKeyColumnArray[0]->Name  ?>';
				$key = $objDbRow->GetColumn($strColumnAlias, '<?php echo $objTable->PrimaryKeyColumnArray[0]->DbType  ?>');
				$objPreviousItemArray = (!empty ($objPreviousItemArray[$key]) ? $objPreviousItemArray[$key] : null);
			}
<?php } ?>			
			
<?php 
	if ($blnImmediateExpansions || $blnExtendedExpansions) { 
?>
			// See if we're doing an array expansion on the previous item
			if ($objExpandAsArrayNode && 
					is_array($objPreviousItemArray) && 
					count($objPreviousItemArray)) {

				if (<?php echo $objTable->ClassName  ?>::ExpandArray ($objDbRow, $strAliasPrefix, $objExpandAsArrayNode, $objPreviousItemArray, $strColumnAliasArray)) {
					return false; // db row was used but no new object was created
				}
			}
<?php 
	} // if 
?>

			// Create a new instance of the <?php echo $objTable->ClassName  ?> object
			$objToReturn = new <?php echo $objTable->ClassName  ?>();
			$objToReturn->__blnRestored = true;

<?php foreach ($objTable->ColumnArray as $objColumn) { ?>
			$strAlias = $strAliasPrefix . '<?php echo $objColumn->Name  ?>';
			$strAliasName = !empty($strColumnAliasArray[$strAlias]) ? $strColumnAliasArray[$strAlias] : $strAlias;
			$objToReturn-><?php echo $objColumn->VariableName  ?> = $objDbRow->GetColumn($strAliasName, '<?php echo $objColumn->DbType  ?>');
<?php if (($objColumn->PrimaryKey) && (!$objColumn->Identity)) { ?>
			$objToReturn->__<?php echo $objColumn->VariableName  ?> = $objDbRow->GetColumn($strAliasName, '<?php echo $objColumn->DbType  ?>');
<?php } ?>
<?php } ?><?php GO_BACK(1); ?>


			// set cache for every unique index
			if (false !== $strCacheKey) {
<?php foreach ($objTable->IndexArray as $objIndex) { ?>
<?php if ($objIndex->Unique) { ?>
<?php $objColumnArray = $objCodeGen->GetColumnArray($objTable, $objIndex->ColumnNameArray); ?>
<?php foreach($objColumnArray as $objColumn) { ?>

				$strAlias = $strAliasPrefix . '<?php echo $objColumn->Name  ?>';
				$strAliasName = !empty($strColumnAliasArray[$strAlias]) ? $strColumnAliasArray[$strAlias] : $strAlias;
				$<?php echo $objColumn->VariableName  ?> = $objDbRow->GetColumn($strAliasName, '<?php echo $objColumn->DbType  ?>');
<?php } ?>

				$strCacheKey = self::CreateCacheKeyHelper(<?php echo $objCodeGen->NamedParameterListFromColumnArray($objColumnArray);  ?>);
				if ($strCacheKey) {
					QApplication::$objCacheProvider->Set($strCacheKey, $objToReturn);
				}
<?php } ?>
<?php } ?>

			}

			if (isset($arrPreviousItems) && is_array($arrPreviousItems)) {
				foreach ($arrPreviousItems as $objPreviousItem) {
<?php foreach ($objTable->PrimaryKeyColumnArray as $col) { ?>
					if ($objToReturn-><?php echo $col->PropertyName  ?> != $objPreviousItem-><?php echo $col->PropertyName  ?>) {
						continue;
					}
<?php } ?>
					// this is a duplicate leaf in a complex join
					return null; // indicates no object created and the db row has not been used
				}
			}
			
			// Instantiate Virtual Attributes
			$strVirtualPrefix = $strAliasPrefix . '__';
			$strVirtualPrefixLength = strlen($strVirtualPrefix);
			foreach ($objDbRow->GetColumnNameArray() as $strColumnName => $mixValue) {
				if (strncmp($strColumnName, $strVirtualPrefix, $strVirtualPrefixLength) == 0)
					$objToReturn->__strVirtualAttributeArray[substr($strColumnName, $strVirtualPrefixLength)] = $mixValue;
			}


			// Prepare to Check for Early/Virtual Binding

			$objExpansionAliasArray = array();
			if ($objExpandAsArrayNode) {
				$objExpansionAliasArray = $objExpandAsArrayNode->ChildNodeArray;
			}

			if (!$strAliasPrefix)
				$strAliasPrefix = '<?php echo $objTable->Name  ?>__';

<?php foreach ($objTable->ColumnArray as $objColumn) { ?>
<?php if ($objColumn->Reference && !$objColumn->Reference->IsType) { ?>
			// Check for <?php echo $objColumn->Reference->PropertyName  ?> Early Binding
			$strAlias = $strAliasPrefix . '<?php echo $objColumn->Name  ?>__<?php echo $objCodeGen->GetTable($objColumn->Reference->Table)->PrimaryKeyColumnArray[0]->Name  ?>';
			$strAliasName = !empty($strColumnAliasArray[$strAlias]) ? $strColumnAliasArray[$strAlias] : $strAlias;
			if (!is_null($objDbRow->GetColumn($strAliasName))) {
				$objExpansionNode = (empty($objExpansionAliasArray['<?php echo $objColumn->Name ?>']) ? null : $objExpansionAliasArray['<?php echo $objColumn->Name ?>']);
				$objToReturn-><?php echo $objColumn->Reference->VariableName ?> = <?php echo $objColumn->Reference->VariableType  ?>::InstantiateDbRow($objDbRow, $strAliasPrefix . '<?php echo $objColumn->Name  ?>__', $objExpansionNode, null, $strColumnAliasArray);
			}
<?php } ?>
<?php } ?>

<?php foreach ($objTable->ReverseReferenceArray as $objReference) { ?><?php if ($objReference->Unique) { ?>
			// Check for <?php echo $objReference->ObjectDescription  ?> Unique ReverseReference Binding
			$strAlias = $strAliasPrefix . '<?php echo strtolower($objReference->ObjectDescription)  ?>__<?php echo $objCodeGen->GetTable($objReference->Table)->PrimaryKeyColumnArray[0]->Name  ?>';
			$strAliasName = !empty($strColumnAliasArray[$strAlias]) ? $strColumnAliasArray[$strAlias] : $strAlias;
			if ($objDbRow->ColumnExists($strAliasName)) {
				if (!is_null($objDbRow->GetColumn($strAliasName))) {
					$objExpansionNode = (empty($objExpansionAliasArray['<?php echo strtolower($objReference->ObjectDescription) ?>']) ? null : $objExpansionAliasArray['<?php echo strtolower($objReference->ObjectDescription) ?>']);
					$objToReturn->obj<?php echo $objReference->ObjectDescription  ?> = <?php echo $objReference->VariableType  ?>::InstantiateDbRow($objDbRow, $strAliasPrefix . '<?php echo strtolower($objReference->ObjectDescription)  ?>__', $objExpansionNode, null, $strColumnAliasArray);
				}
				else {
					// We ATTEMPTED to do an Early Bind but the Object Doesn't Exist
					// Let's set to FALSE so that the object knows not to try and re-query again
					$objToReturn->obj<?php echo $objReference->ObjectDescription  ?> = false;
				}
			}

<?php } ?><?php } ?>
				
<?php foreach ($objTable->ManyToManyReferenceArray as $objReference) { ?>
			// Check for <?php echo $objReference->ObjectDescription  ?> Virtual Binding
			$strAlias = $strAliasPrefix . '<?php echo strtolower($objReference->ObjectDescription)  ?>__<?php echo $objReference->OppositeColumn  ?>__<?php echo $objCodeGen->GetTable($objReference->AssociatedTable)->PrimaryKeyColumnArray[0]->Name  ?>';
			$strAliasName = !empty($strColumnAliasArray[$strAlias]) ? $strColumnAliasArray[$strAlias] : $strAlias;
			$objExpansionNode = (empty($objExpansionAliasArray['<?php echo strtolower($objReference->ObjectDescription) ?>']) ? null : $objExpansionAliasArray['<?php echo strtolower($objReference->ObjectDescription) ?>']);
			$blnExpanded = ($objExpansionNode && $objExpansionNode->ExpandAsArray);
			if ($blnExpanded && null === $objToReturn->_obj<?php echo $objReference->ObjectDescription  ?>Array) {
				$objToReturn->_obj<?php echo $objReference->ObjectDescription  ?>Array = array();
			}
			if (!is_null($objDbRow->GetColumn($strAliasName))) {
				if ($blnExpanded) {
					$objToReturn->_obj<?php echo $objReference->ObjectDescription  ?>Array[] = <?php echo $objReference->VariableType  ?>::InstantiateDbRow($objDbRow, $strAliasPrefix . '<?php echo strtolower($objReference->ObjectDescription)  ?>__<?php echo $objReference->OppositeColumn  ?>__', $objExpansionNode, null, $strColumnAliasArray);
				} elseif (is_null($objToReturn->_obj<?php echo $objReference->ObjectDescription  ?>)) {
					$objToReturn->_obj<?php echo $objReference->ObjectDescription  ?> = <?php echo $objReference->VariableType  ?>::InstantiateDbRow($objDbRow, $strAliasPrefix . '<?php echo strtolower($objReference->ObjectDescription)  ?>__<?php echo $objReference->OppositeColumn  ?>__', $objExpansionNode, null, $strColumnAliasArray);
				}
			}

<?php } ?>

<?php foreach ($objTable->ReverseReferenceArray as $objReference) { ?><?php if (!$objReference->Unique) { ?>
			// Check for <?php echo $objReference->ObjectDescription  ?> Virtual Binding
			$strAlias = $strAliasPrefix . '<?php echo strtolower($objReference->ObjectDescription)  ?>__<?php echo $objCodeGen->GetTable($objReference->Table)->PrimaryKeyColumnArray[0]->Name  ?>';
			$strAliasName = !empty($strColumnAliasArray[$strAlias]) ? $strColumnAliasArray[$strAlias] : $strAlias;
			$objExpansionNode = (empty($objExpansionAliasArray['<?php echo strtolower($objReference->ObjectDescription) ?>']) ? null : $objExpansionAliasArray['<?php echo strtolower($objReference->ObjectDescription) ?>']);
			$blnExpanded = ($objExpansionNode && $objExpansionNode->ExpandAsArray);
			if ($blnExpanded && null === $objToReturn->_obj<?php echo $objReference->ObjectDescription  ?>Array)
				$objToReturn->_obj<?php echo $objReference->ObjectDescription  ?>Array = array();
			if (!is_null($objDbRow->GetColumn($strAliasName))) {
				if ($blnExpanded) {
					$objToReturn->_obj<?php echo $objReference->ObjectDescription  ?>Array[] = <?php echo $objReference->VariableType  ?>::InstantiateDbRow($objDbRow, $strAliasPrefix . '<?php echo strtolower($objReference->ObjectDescription)  ?>__', $objExpansionNode, null, $strColumnAliasArray);
				} elseif (is_null($objToReturn->_obj<?php echo $objReference->ObjectDescription  ?>)) {
					$objToReturn->_obj<?php echo $objReference->ObjectDescription  ?> = <?php echo $objReference->VariableType  ?>::InstantiateDbRow($objDbRow, $strAliasPrefix . '<?php echo strtolower($objReference->ObjectDescription)  ?>__', $objExpansionNode, null, $strColumnAliasArray);
				}
			}

<?php } ?><?php } ?>
			return $objToReturn;
		}
		
		/**
		 * Instantiate an array of <?php echo $objTable->ClassNamePlural  ?> from a Database Result
		 * @param DatabaseResultBase $objDbResult
		 * @param QQBaseNode $objExpandAsArrayNode
		 * @param string[] $strColumnAliasArray
		 * @return <?php echo $objTable->ClassName  ?>[]
		 */
		public static function InstantiateDbResult(QDatabaseResultBase $objDbResult, $objExpandAsArrayNode = null, $strColumnAliasArray = null) {
			$objToReturn = array();

			if (!$strColumnAliasArray)
				$strColumnAliasArray = array();

			// If blank resultset, then return empty array
			if (!$objDbResult)
				return $objToReturn;

			// Load up the return array with each row
			if ($objExpandAsArrayNode) {
				$objToReturn = array();
				$objPrevItemArray = array();
				while ($objDbRow = $objDbResult->GetNextRow()) {
					$objItem = <?php echo $objTable->ClassName  ?>::InstantiateDbRow($objDbRow, null, $objExpandAsArrayNode, $objPrevItemArray, $strColumnAliasArray);
					if ($objItem) {
						$objToReturn[] = $objItem;
<?php if ($objTable->PrimaryKeyColumnArray)  {?>
						$objPrevItemArray[$objItem-><?php echo $objTable->PrimaryKeyColumnArray[0]->VariableName ?>][] = $objItem;
<?php } else { ?>
						$objPrevItemArray[] = $objItem;
		
<?php } ?>		
					}
				}
			} else {
				while ($objDbRow = $objDbResult->GetNextRow())
					$objToReturn[] = <?php echo $objTable->ClassName  ?>::InstantiateDbRow($objDbRow, null, null, null, $strColumnAliasArray);
			}

			return $objToReturn;
		}


		/**
		 * Instantiate a single <?php echo $objTable->ClassName  ?> object from a query cursor (e.g. a DB ResultSet).
		 * Cursor is automatically moved to the "next row" of the result set.
		 * Will return NULL if no cursor or if the cursor has no more rows in the resultset.
		 * @param QDatabaseResultBase $objDbResult cursor resource
		 * @return <?php echo $objTable->ClassName  ?> next row resulting from the query
		 */
		public static function InstantiateCursor(QDatabaseResultBase $objDbResult) {
			// If blank resultset, then return empty result
			if (!$objDbResult) return null;

			// If empty resultset, then return empty result
			$objDbRow = $objDbResult->GetNextRow();
			if (!$objDbRow) return null;

			// We need the Column Aliases
			$strColumnAliasArray = $objDbResult->QueryBuilder->ColumnAliasArray;
			if (!$strColumnAliasArray) $strColumnAliasArray = array();

			// Pull Expansions
			$objExpandAsArrayNode = $objDbResult->QueryBuilder->ExpandAsArrayNode;
			if (!empty ($objExpandAsArrayNode)) {
				throw new QCallerException ("Cannot use InstantiateCursor with ExpandAsArray");
			}

			// Load up the return result with a row and return it
			return <?php echo $objTable->ClassName  ?>::InstantiateDbRow($objDbRow, null, null, null, $strColumnAliasArray);
		}
