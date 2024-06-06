<?php

/*
 * This file is part of the Access to Memory (AtoM) software.
 *
 * Access to Memory (AtoM) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Access to Memory (AtoM) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Access to Memory (AtoM).  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * CSV language column test. Check if present, check values against master list.
 *
 * @author     Steve Breker <sbreker@artefactual.com>
 */
class CsvLanguageValidator extends CsvBaseValidator
{
    public const TITLE = 'Language Check';
    public const LIMIT_TO = ['QubitInformationObject', 'QubitRepository'];

    protected $languages = [];
    protected $rowsWithInvalidLanguage = 0;
    protected $invalidLanguages = [];

    public function __construct(?array $options = null)
    {
        $this->setTitle(self::TITLE);
        parent::__construct($options);

        $this->languages = array_keys(sfCultureInfo::getInstance()->getLanguages());
        $this->setRequiredColumns(['language']);
    }

    public function reset()
    {
        $this->rowsWithPipeFoundInLanguage = 0;
        $this->rowsWithInvalidLanguage = 0;
        $this->invalidLanguages = [];

        parent::reset();
    }

    public function testRow(array $header, array $row)
    {
        if (!parent::testRow($header, $row)) {
            return;
        }

        $row = $this->combineRow($header, $row);

        if (empty($row['language'])) {
            return;
        }

        // Validate language value against AtoM.
        $errorDetailAdded = false;
        foreach (explode('|', $row['language']) as $value) {
            if ($this->isLanguageValid($value)) {
                continue;
            }

            if (!$errorDetailAdded) {
                ++$this->rowsWithInvalidLanguage;
                $this->appendToCsvRowList();
                $errorDetailAdded = true;
            }

            // Keep a list of invalid language values.
            if (!in_array(trim($value), $this->invalidLanguages)) {
                $this->invalidLanguages[] = trim($value);
            }
        }
    }

    public function getTestResult()
    {
        if (!$this->columnPresent('language')) {
            // language column not present in file.
            $this->testData->addResult(sprintf("'language' column not present in file."));

            return parent::getTestResult();
        }

        if ($this->columnDuplicated('language')) {
            $this->appendDuplicatedColumnError('language');

            return parent::getTestResult();
        }

        // Rows exist with invalid language.
        if (0 < $this->rowsWithInvalidLanguage) {
            $this->testData->setStatusError();
            $this->testData->addResult(sprintf('Rows with invalid language values: %s', $this->rowsWithInvalidLanguage));
            $this->testData->addResult(sprintf('Invalid language values: %s', implode(', ', $this->invalidLanguages)));
        }

        if (0 === $this->rowsWithInvalidLanguage) {
            $this->testData->addResult(sprintf("'language' column values are all valid."));
        }

        if (!empty($this->getCsvRowList())) {
            $this->testData->addDetail(sprintf('CSV row numbers where issues were found: %s', implode(', ', $this->getCsvRowList())));
        }

        return parent::getTestResult();
    }

    protected function isLanguageValid(string $language)
    {
        $language = trim($language);

        if ('' === trim($language)) {
            return false;
        }

        return in_array($language, $this->languages);
    }
}
