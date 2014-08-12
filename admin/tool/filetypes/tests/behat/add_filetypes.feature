@tool @tool_filetypes
Feature: Add customised file types
  In order to support a file mime type which doesn't exist in Moodle
  As an administrator
  I need to add a new customised file type

  Background:
    Given I log in as "admin"
    And I navigate to "Custom file types" node in "Site administration > Appearance"

  Scenario: Add a new file type
    Given I press "Add"
    And I set the following fields to these values:
      | File extension   | ggb8                          |
      | File description | GeoGebra                      |
      | File MIME type   | application/vnd.geogebra.file |
      | File icon        | archive                       |
    When I press "Save changes"
    Then I should see "GeoGebra"

  Scenario: Update an existing custom file type
    Given I press "Add"
    And I set the following fields to these values:
      | File extension   | mobi8                          |
      | File description | Kindle ebook                   |
      | File MIME type   | application/x-mobipocket-ebook |
      | File icon        | pdf                            |
    And I press "Save changes"
    And I should see "mobi8"
    And I click on "Update" "link"
    And I set the following fields to these values:
      | File extension | pdf |
    And I press "Save changes"
    And I should see "File extensions must be unique"
    And I set the following fields to these values:
      | File extension | ggb8 |
    When I press "Save changes"
    Then I should see "ggb8"

  Scenario: Delete an existing custom file type
    Given I press "Add"
    And I set the following fields to these values:
      | File extension   | mobi8                          |
      | File description | Kindle ebook                   |
      | File MIME type   | application/x-mobipocket-ebook |
      | File icon        | pdf                            |
    And I press "Save changes"
    And I should see "mobi8"
    And I click on "Delete" "link"
    And I should see "Are you absolutely sure you want to remove mobi8?"
    When I press "Yes"
    Then I should not see "ggb8"

  @javascript
  Scenario: Create a resource activity which contains a customised file type
    Given I press "Add"
    And I set the following fields to these values:
      | File extension   | mobi8                          |
      | File description | Kindle ebook                   |
      | File MIME type   | application/x-mobipocket-ebook |
      | File icon        | pdf                            |
    And I press "Save changes"
    And I should see "mobi8"
    And I log out
    # Create a resource activity and add it to a course
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username | firstname |
      | teacher1 | teacher   |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    When I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "File" to section "1"
    And I set the following fields to these values:
      | Name        | An example of customised file type |
      | Description | File description                   |
    And I upload "admin/tool/filetypes/tests/fixtures/test.mobi8" file to "Select files" filemanager
    And I expand all fieldsets
    And I set the field "Show type" to "1"
    And I set the field "Display resource description" to "1"
    And I press "Save and return to course"
    Then I should see "Kindle ebook"
    Then the "src" attribute of ".modtype_resource a img" "css_element" should contain "pdf"
