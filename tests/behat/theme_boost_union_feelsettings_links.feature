@theme @theme_learnr @theme_learnr_feelsettings @theme_learnr_feelsettings_links
Feature: Configuring the theme_learnr plugin for the "Links" tab on the "Feel" page
  In order to use the features
  As admin
  I need to be able to configure the theme Boost Union plugin

  Background:
    Given the following "users" exist:
      | username |
      | student1 |
      | teacher1 |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |

  # Unfortunately, this can't be tested with Behat yet
  # And as this feature file for this tab can't be empty, we just add a dummy step.
  Scenario: Setting: Mark external links
    When I log in as "admin"
