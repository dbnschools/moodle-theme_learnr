@theme @theme_learnr @theme_learnr_contentsettings @theme_learnr_contentsettings_infobanners
Feature: Configuring the theme_learnr plugin for the "Information banners" tab on the "Content" page
  In order to use the features
  As admin
  I need to be able to configure the theme LearnR plugin

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

  Scenario: Setting: Information banners - Display info banner 1 on all available pages
    Given the following config values are set as admin:
      | config             | value                                        | plugin            |
      | infobanner1enabled | yes                                          | theme_learnr |
      | infobanner1content | "This is a test content"                     | theme_learnr |
      | infobanner1pages   | mydashboard,mycourses,frontpage,course,login | theme_learnr |
      | infobanner1mode    | perp                                         | theme_learnr |
    When I log in as "teacher1"
    And I am on site homepage
    Then I should see "This is a test content" in the "#themeboostunioninfobanner1" "css_element"
    And I follow "Dashboard"
    Then I should see "This is a test content" in the "#themeboostunioninfobanner1" "css_element"
    And I follow "My courses"
    Then I should see "This is a test content" in the "#themeboostunioninfobanner1" "css_element"
    When I am on "Course 1" course homepage
    Then I should see "This is a test content" in the "#themeboostunioninfobanner1" "css_element"
    When I log out
    And I click on "Log in" "link" in the ".logininfo" "css_element"
    Then I should see "This is a test content" in the "#themeboostunioninfobanner1" "css_element"

  Scenario: Setting: Information banners - Display info banner 1 only on one available page
    Given the following config values are set as admin:
      | config             | value                    | plugin            |
      | infobanner1enabled | yes                      | theme_learnr |
      | infobanner1content | "This is a test content" | theme_learnr |
      | infobanner1pages   | mydashboard              | theme_learnr |
      | infobanner1mode    | perp                     | theme_learnr |
    When I log in as "teacher1"
    And I am on site homepage
    Then I should not see "This is a test content"
    And I follow "Dashboard"
    Then I should see "This is a test content" in the "#themeboostunioninfobanner1" "css_element"
    When I am on "Course 1" course homepage
    Then I should not see "This is a test content"
    When I log out
    And I click on "Log in" "link" in the ".logininfo" "css_element"
    Then I should not see "This is a test content"

  Scenario: Setting: Information banners - Display info banner 1 with the different bootstrap color classes
    Given the following config values are set as admin:
      | config             | value                    | plugin            |
      | infobanner1enabled | yes                      | theme_learnr |
      | infobanner1content | "This is a test content" | theme_learnr |
      | infobanner1pages   | mydashboard              | theme_learnr |
      | infobanner1mode    | perp                     | theme_learnr |
      | infobanner1bsclass | primary                  | theme_learnr |
    When I log in as "teacher1"
    And I follow "Dashboard"
    Then I should see "This is a test content" in the "#themeboostunioninfobanner1" "css_element"
    And the "class" attribute of "#themeboostunioninfobanner1" "css_element" should contain "primary"
    And I log out
    Given the following config values are set as admin:
      | config             | value     | plugin            |
      | infobanner1bsclass | secondary | theme_learnr |
    When I log in as "teacher1"
    And I follow "Dashboard"
    Then I should see "This is a test content" in the "#themeboostunioninfobanner1" "css_element"
    And the "class" attribute of "#themeboostunioninfobanner1" "css_element" should contain "secondary"
    And I log out
    Given the following config values are set as admin:
      | config             | value   | plugin            |
      | infobanner1bsclass | success | theme_learnr |
    When I log in as "teacher1"
    And I follow "Dashboard"
    Then I should see "This is a test content" in the "#themeboostunioninfobanner1" "css_element"
    And the "class" attribute of "#themeboostunioninfobanner1" "css_element" should contain "success"
    And I log out
    Given the following config values are set as admin:
      | config             | value  | plugin            |
      | infobanner1bsclass | danger | theme_learnr |
    When I log in as "teacher1"
    And I follow "Dashboard"
    Then I should see "This is a test content" in the "#themeboostunioninfobanner1" "css_element"
    And the "class" attribute of "#themeboostunioninfobanner1" "css_element" should contain "danger"
    And I log out
    Given the following config values are set as admin:
      | config             | value   | plugin            |
      | infobanner1bsclass | warning | theme_learnr |
    When I log in as "teacher1"
    And I follow "Dashboard"
    Then I should see "This is a test content" in the "#themeboostunioninfobanner1" "css_element"
    And the "class" attribute of "#themeboostunioninfobanner1" "css_element" should contain "warning"
    And I log out
    Given the following config values are set as admin:
      | config             | value | plugin            |
      | infobanner1bsclass | info  | theme_learnr |
    When I log in as "teacher1"
    And I follow "Dashboard"
    Then I should see "This is a test content" in the "#themeboostunioninfobanner1" "css_element"
    And the "class" attribute of "#themeboostunioninfobanner1" "css_element" should contain "info"
    And I log out
    Given the following config values are set as admin:
      | config             | value | plugin            |
      | infobanner1bsclass | light | theme_learnr |
    When I log in as "teacher1"
    And I follow "Dashboard"
    Then I should see "This is a test content" in the "#themeboostunioninfobanner1" "css_element"
    And the "class" attribute of "#themeboostunioninfobanner1" "css_element" should contain "light"
    And I log out
    Given the following config values are set as admin:
      | config             | value | plugin            |
      | infobanner1bsclass | dark  | theme_learnr |
    When I log in as "teacher1"
    And I follow "Dashboard"
    Then I should see "This is a test content" in the "#themeboostunioninfobanner1" "css_element"
    And the "class" attribute of "#themeboostunioninfobanner1" "css_element" should contain "dark"
    And I log out
    Given the following config values are set as admin:
      | config             | value | plugin            |
      | infobanner1bsclass | none  | theme_learnr |
    When I log in as "teacher1"
    And I follow "Dashboard"
    Then I should see "This is a test content" in the "#themeboostunioninfobanner1" "css_element"
    And the "class" attribute of "#themeboostunioninfobanner1" "css_element" should contain "none"
    And I log out

  Scenario: Setting: Information banners - Do not enable the info banner 1 at all.
    Given the following config values are set as admin:
      | config             | value                              | plugin            |
      | infobanner1enabled | no                                 | theme_learnr |
      | infobanner1content | "This is a test content"           | theme_learnr |
      | infobanner1pages   | mydashboard,frontpage,course,login | theme_learnr |
      | infobanner1mode    | perp                               | theme_learnr |
    When I log in as "teacher1"
    And I follow "Dashboard"
    Then I should not see "This is a test content"

  @javascript
  Scenario: Setting: Information banners - Make the info banner 1 dismissible
    Given the following config values are set as admin:
      | config                 | value                    | plugin            |
      | infobanner1enabled     | yes                      | theme_learnr |
      | infobanner1content     | "This is a test content" | theme_learnr |
      | infobanner1pages       | mydashboard              | theme_learnr |
      | infobanner1mode        | perp                     | theme_learnr |
      | infobanner1dismissible | yes                      | theme_learnr |
    When I log in as "teacher1"
    And I follow "Dashboard"
    And I should see "This is a test content" in the "#themeboostunioninfobanner1" "css_element"
    And I click on "#themeboostunioninfobanner1close" "css_element"
    Then I should not see "This is a test content"
    And I reload the page
    And I should not see "This is a test content"

  @javascript
  Scenario: Setting: Information banners - Do not make the info banner 1 dismissible (countercheck)
    Given the following config values are set as admin:
      | config                 | value                    | plugin            |
      | infobanner1enabled     | yes                      | theme_learnr |
      | infobanner1content     | "This is a test content" | theme_learnr |
      | infobanner1pages       | mydashboard              | theme_learnr |
      | infobanner1mode        | perp                     | theme_learnr |
      | infobanner1dismissible | no                       | theme_learnr |
    When I log in as "teacher1"
    And I follow "Dashboard"
    And I should see "This is a test content" in the "#themeboostunioninfobanner1" "css_element"
    And "#themeboostunioninfobanner1close" "css_element" should not exist

  @javascript
  Scenario: Setting: Information banners - Reset dismissed info banner 1
    Given the following config values are set as admin:
      | config                 | value                    | plugin            |
      | infobanner1enabled     | yes                      | theme_learnr |
      | infobanner1content     | "This is a test content" | theme_learnr |
      | infobanner1pages       | mydashboard              | theme_learnr |
      | infobanner1mode        | perp                     | theme_learnr |
      | infobanner1dismissible | yes                      | theme_learnr |
    When I log in as "teacher1"
    And I follow "Dashboard"
    And I should see "This is a test content" in the "#themeboostunioninfobanner1" "css_element"
    And I click on "#themeboostunioninfobanner1close" "css_element"
    Then I should not see "This is a test content"
    And I reload the page
    And I should not see "This is a test content"
    And I log out
    When I log in as "admin"
    # We deactivate debugging for a while as the Behat step would otherwise fail due to the
    # stupid 'Too much data passed as arguments to js_call_amd' debugging message which can't be avoided
    # on this settings page. This debugging message can't be avoided as we simple use too much hide_if() there.
    And the following config values are set as admin:
      | debug          | 0 |
      | debugdisplay   | 0 |
    And I navigate to "Appearance > LearnR > Content" in site administration
    And I click on "Info banner" "link" in the "#adminsettings .nav-tabs" "css_element"
    And I click on "Reset visibility of info banner 1" "link"
    And I click on "Confirm" "link"
    Then I should see "The visibility of info banner 1 has been reset"
    # We reactivate debugging again.
    And the following config values are set as admin:
      | debug          | 32767 |
      | debugdisplay   | 1     |
    And I log out
    When I log in as "teacher1"
    And I follow "Dashboard"
    Then I should see "This is a test content" in the "#themeboostunioninfobanner1" "css_element"

  Scenario: Setting: Information banners - Display multiple, but not all info banners
    Given the following config values are set as admin:
      | config             | value                        | plugin            |
      | infobanner1enabled | yes                          | theme_learnr |
      | infobanner1content | "This is the first content"  | theme_learnr |
      | infobanner1pages   | mydashboard                  | theme_learnr |
      | infobanner1mode    | perp                         | theme_learnr |
      | infobanner2enabled | no                           | theme_learnr |
      | infobanner3enabled | yes                          | theme_learnr |
      | infobanner3content | "This is the second content" | theme_learnr |
      | infobanner3pages   | mydashboard                  | theme_learnr |
      | infobanner3mode    | perp                         | theme_learnr |
      | infobanner4enabled | no                           | theme_learnr |
      | infobanner5enabled | no                           | theme_learnr |
    When I log in as "teacher1"
    And I follow "Dashboard"
    Then I should see "This is the first content" in the "#themeboostunioninfobanner1" "css_element"
    And I should see "This is the second content" in the "#themeboostunioninfobanner3" "css_element"
    And "#themeboostunioninfobanner2" "css_element" should not exist
    And "#themeboostunioninfobanner4" "css_element" should not exist
    And "#themeboostunioninfobanner5" "css_element" should not exist
    And "This is the first content" "text" should appear before "This is the second content" "text"

  Scenario: Setting: Information banners - Display multiple banners in changed order
    Given the following config values are set as admin:
      | config             | value                        | plugin            |
      | infobanner1enabled | yes                          | theme_learnr |
      | infobanner1content | "This is the third content"  | theme_learnr |
      | infobanner1pages   | mydashboard                  | theme_learnr |
      | infobanner1order   | 2                            | theme_learnr |
      | infobanner1mode    | perp                         | theme_learnr |
      | infobanner2enabled | yes                          | theme_learnr |
      | infobanner2content | "This is the first content"  | theme_learnr |
      | infobanner2pages   | mydashboard                  | theme_learnr |
      | infobanner2order   | 1                            | theme_learnr |
      | infobanner2mode    | perp                         | theme_learnr |
      | infobanner3enabled | yes                          | theme_learnr |
      | infobanner3content | "This is the second content" | theme_learnr |
      | infobanner3pages   | mydashboard                  | theme_learnr |
      | infobanner3order   | 1                            | theme_learnr |
      | infobanner3mode    | perp                         | theme_learnr |
      | infobanner4enabled | yes                          | theme_learnr |
      | infobanner4content | "This is the fourth content" | theme_learnr |
      | infobanner4pages   | mydashboard                  | theme_learnr |
      | infobanner4order   | 2                            | theme_learnr |
      | infobanner4mode    | perp                         | theme_learnr |
    When I log in as "teacher1"
    And I follow "Dashboard"
    Then I should see "This is the third content" in the "#themeboostunioninfobanner1" "css_element"
    And I should see "This is the first content" in the "#themeboostunioninfobanner2" "css_element"
    And I should see "This is the second content" in the "#themeboostunioninfobanner3" "css_element"
    And I should see "This is the fourth content" in the "#themeboostunioninfobanner4" "css_element"
    And "#themeboostunioninfobanner5" "css_element" should not exist
    And "This is the first content" "text" should appear before "This is the second content" "text"
    And "This is the second content" "text" should appear before "This is the third content" "text"
    And "This is the third content" "text" should appear before "This is the fourth content" "text"

  Scenario: Setting: Information banners - Display info banner 1 on a time based setting, don't show it yet as the display time is not reached yet.
    Given the following config values are set as admin:
      | config             | value                       | plugin            |
      | infobanner1enabled | yes                         | theme_learnr |
      | infobanner1content | "This is a test content"    | theme_learnr |
      | infobanner1pages   | mydashboard                 | theme_learnr |
      | infobanner1mode    | time                        | theme_learnr |
      | infobanner1start   | ##first day of next month## | theme_learnr |
      | infobanner1end     | ##last day of next month##  | theme_learnr |
    When I log in as "teacher1"
    And I follow "Dashboard"
    Then I should not see "This is a test content"

  Scenario: Setting: Information banners - Display info banner 1 on a time based setting, show it as we are within the display time.
    Given the following config values are set as admin:
      | config             | value                       | plugin            |
      | infobanner1enabled | yes                         | theme_learnr |
      | infobanner1content | "This is a test content"    | theme_learnr |
      | infobanner1pages   | mydashboard                 | theme_learnr |
      | infobanner1mode    | time                        | theme_learnr |
      | infobanner1start   | ##last day of last month##  | theme_learnr |
      | infobanner1end     | ##first day of next month## | theme_learnr |
    When I log in as "teacher1"
    And I follow "Dashboard"
    Then I should see "This is a test content" in the "#themeboostunioninfobanner1" "css_element"

  Scenario: Setting: Information banners - Display info banner 1 on a time based setting, don't show it anymore as the display time is already over.
    Given the following config values are set as admin:
      | config             | value                       | plugin            |
      | infobanner1enabled | yes                         | theme_learnr |
      | infobanner1content | "This is a test content"    | theme_learnr |
      | infobanner1pages   | mydashboard                 | theme_learnr |
      | infobanner1mode    | time                        | theme_learnr |
      | infobanner1start   | ##first day of last month## | theme_learnr |
      | infobanner1end     | ##last day of last month##  | theme_learnr |
    When I log in as "teacher1"
    And I follow "Dashboard"
    Then I should not see "This is a test content"
