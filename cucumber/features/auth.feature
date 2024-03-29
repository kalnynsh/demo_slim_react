Feature: Login
  In order to check login
  As a guest user
  I want to be able authenticate

  Scenario: Without login button
    Given I am a guest user
    And I do not have "OAUTH" feature
    When I open "/" page
    Then I see "Auction" header
    And I see "join-link" element
    And I do not see "login-button" element
    And I do not see "logout-button" element

  Scenario: View login button
    Given I am a guest user
    And I have "OAUTH" feature
    When I open "/" page
    Then I see "Auction" header
    And I see "join-link" element
    And I see "login-button" element
    And I do not see "logout-button" element

  Scenario: Login
    Given I am a guest user
    And I have "OAUTH" feature
    And I am on "/" page
    When I click "login-button" element
    Then I see "authorize-page" element

    When I fill "email" field with "john_crishum@info.org"
    And I fill "password" field with "secret"
    And I click sumbit button
    Then I see "logout-button" element
    And I do not see "login-button" element
    And I do not see "join-link" element

  Scenario: Authenticated user
    Given I am the user
    And I have "OAUTH" feature
    And I am on "/" page
    Then I see "logout-button" element
    And I do not see "login-button" element
    And I do not see "join-link" element

    When I click "logout-button" element
    Then I see "login-button" element
    And I do not see "logout-button" element
    And I see "join-link" element

  Scenario: Logout
    Given I am the user
    And I have "OAUTH" feature
    And I am on "/" page
    And I click "logout-button" element
    Then I see "login-button" element
    And I do not see "logout-button" element