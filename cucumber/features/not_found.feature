Feature: Not found page

  Scenario: Not found page
    Given I am guest user
    When I open "/not-found" page
    Then I see "Page is not found"