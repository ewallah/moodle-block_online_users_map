@block @block_online_users_map
Feature: The online users map block allow you to see who is currently online on frontpage
  In order to enable the online users map block on the front page page
  As an admin
  I can add the online users block to the front page page

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                | country |
      | student1 | Student   | 1        | student1@example.com | BE      |
      | student2 | Student   | 2        | student2@example.com | AU      |
    And the following "course enrolments" exist:
      | user | course | role           |
      | student1 | C1 | student        |
    And the following config values are set as admin:
      | googlemapkey3 | faketestkey |

  @javascript
  Scenario: Not view the online users block on the front page
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Online Users Map" block
    And I add the "Online users" block
    Then I should see "Where our students come from"
    And I log out
    And I log in as "student1"
    And I am on site homepage
    And I log out
    And I log in as "student2"
    And I am on site homepage
    And I log out
    And I trigger cron
    And I trigger cron
    And I log in as "student1"
    And I am on site homepage
    And I wait "1" seconds
    Then I should see "Where our students come from"
