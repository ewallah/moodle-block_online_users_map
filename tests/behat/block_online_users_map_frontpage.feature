@block @block_online_users_map
Feature: The online users map block allow you to see who is currently online on frontpage
  In order to enable the online users map block on the front page page
  As an admin
  I can add the online users block to the front page page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                | country |
      | student1 | Student   | 1        | student1@example.com | BE      |
      | student2 | Student   | 2        | student2@example.com | AU      |

  @javascript
  Scenario: Not view the online users block on the front page
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Online Users Map" block
    And I log out
    And I trigger cron
    And I log in as "student1"
    And I am on site homepage
    # TODO:  review why not visible
    And I should not see "Where our students come from"
