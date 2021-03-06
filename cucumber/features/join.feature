Feature: View join page

    Scenario: View join page without feature
        Given I am a guest user
        And I do not have "JOIN_TO_US" feature
        When I open "/join" page
        Then I see "Page is not found"

    Scenario: View join page
        Given I am a guest user
        And I have "JOIN_TO_US" feature
        When I open "/join" page
        Then I see "Join to us" header
        And I see "join-form" element

    @wip
    Scenario: Success join
        Given I am a guest user
        And I have "JOIN_TO_US" feature
        And I am on "/join" page
        When I fill "email" field with "join-new@app.test"
        And I fill "password" field with "new-Password-583"
        And I check "agree" checkbox
        And I click sumbit button
        Then I see success "Confirm join by link in email."

    Scenario: Existing join
        Given I am a guest user
        And I have "JOIN_TO_US" feature
        And I am on "/join" page
        When I fill "email" field with "join-existing@app.test"
        And I fill "password" field with "new-Password-864"
        And I check "agree" checkbox
        And I click sumbit button
        Then I see error "User already exists."

    Scenario: Not valid join
        Given I am a guest user
        And I have "JOIN_TO_US" feature
        And I am on "/join" page
        When I fill "email" field with "join-not-valid@app.test"
        And I fill "password" field with "new"
        And I check "agree" checkbox
        And I click sumbit button
        Then I see validation error "This value is too short. It should have 6 characters or more."
