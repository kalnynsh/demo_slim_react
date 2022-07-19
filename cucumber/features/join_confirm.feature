Feature: Join confirm

    Background:
        Given: I am guest user
        And I have "JOIN_TO_US" feature

    @wip
    Scenario: Success confirm
        When I open "/join/confirm?token=00000000-0000-0000-0000-200000000001" page
        Then I see success "Success!"

    Scenario: Expired confirm
        When I open "/join/confirm?token=00000000-0000-0000-0000-200000000002" page
        Then I see error "Token was expired."

    Scenario: Not valid confirm
        When I open "/join/confirm?token=00000000-0000-0000-0000-200000000003" page
        Then I see error "Incorrect token."
