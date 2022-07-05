Feature: View home page
    In order to check home page content
    As guest user
    I want to be able to view home page

    @smoke
    Scenario: View home page content
        Given I am a guest user
        And I do not have "JOIN_TO_US" feature
        When I open "/" page
        Then I see "Auction" header
        And I see "We shall be here soon"
        And I do not see "We are here"

    Scenario: View new home page content
        Given I am a guest user
        And I have "JOIN_TO_US" feature
        When I open "/" page
        Then I see "Auction" header
        And I do not see "We shall be here soon"
        And I see "We are here"
