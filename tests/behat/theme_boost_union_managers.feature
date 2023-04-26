@theme @theme_learnr @theme_learnr_managers
Feature: Configuring the theme_learnr plugin as manager
  In order to use the features
  As manager
  I need to be able to configure the theme LearnR plugin

  Background:
    Given the following "users" exist:
      | username |
      | manager  |
    Given the following "system role assigns" exist:
      | user    | role    | contextlevel |
      | manager | manager | System       |

  Scenario: Capabilities - Allow managers to configure LearnR
    Given the following "permission overrides" exist:
      | capability                  | permission | role    | contextlevel | reference |
      | theme/learnr:configure | Allow      | manager | System       |           |
    And I log in as "manager"
    And I follow "Site administration"
    Then ".secondary-navigation li[data-key='appearance']" "css_element" should exist
    # We just need to test the 'look' page as a representative of all theme admin pages.
    And I navigate to "Appearance > Themes > LearnR > Look" in site administration
    And "body#page-admin-setting-theme_learnr_look" "css_element" should exist
    And I should see "Look" in the "#region-main" "css_element"
    And I should see "General settings" in the "#region-main" "css_element"
    # However, we have to test the 'flavours' page as well as this is an external admin page.
    And I navigate to "Appearance > Themes > LearnR > Flavours" in site administration
    And "body#page-admin-theme-learnr-flavours-overview" "css_element" should exist
    And I should see "Flavours" in the "#region-main" "css_element"
    And I should see "Create flavour" in the "#region-main" "css_element"

  Scenario: Capabilities - Do not allow managers to configure LearnR (countercheck)
    Given the following "permission overrides" exist:
      | capability                  | permission | role    | contextlevel | reference |
      | theme/learnr:configure | Prevent    | manager | System       |           |
    And I log in as "manager"
    And I follow "Site administration"
    Then ".secondary-navigation li[data-key='appearance']" "css_element" should not exist
