@managing_promotions
Feature: Promotion validation
    In order to avoid making mistakes when managing a promotion
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a new promotion without specifying its code
        When I want to create a new promotion
        And I name it "No-VAT promotion"
        And I do not specify its code
        And I try to add it
        Then I should be notified that code is required
        And promotion with name "No-VAT promotion" should not be added

    @ui
    Scenario: Trying to add a new promotion without specifying its name
        When I want to create a new promotion
        And I specify its code as "no_vat_promotion"
        But I do not name it
        And I try to add it
        Then I should be notified that name is required
        And promotion with code "no_vat_promotion" should not be added

    @ui
    Scenario: Adding a promotion with start date set up after end date
        When I want to create a new promotion
        And I specify its code as "FULL_METAL_PROMOTION"
        And I name it "Full metal promotion"
        And I make it available from "24.12.2017" to "12.12.2017"
        And I try to add it
        Then I should be notified that promotion cannot end before it start

    @ui
    Scenario: Trying to remove name from existing promotion
        Given there is a promotion "Christmas sale"
        When I want to modify this promotion
        And I remove its name
        And I try to save my changes
        Then I should be notified that name is required
        And this promotion should still be named "Christmas sale"

    @ui
    Scenario: Trying to add start later then end date for existing promotion
        Given there is a promotion "Christmas sale"
        When I want to modify this promotion
        And I make it available from "24.12.2017" to "12.12.2017"
        And I try to save my changes
        Then I should be notified that promotion cannot end before it start

    @ui @javascript
    Scenario: Trying to add a new promotion without specifying a order percentage discount
        When I want to create a new promotion
        And I specify its code as "christmas_sale"
        And I name it "Christmas sale"
        And I add the "Order percentage discount" action configured without a percentage value
        And I try to add it
        Then I should be notified that this value should not be blank
        And promotion with name "Christmas sale" should not be added

    @ui @javascript
    Scenario: Trying to add a new promotion without specifying an item percentage discount
        When I want to create a new promotion
        And I specify its code as "christmas_sale"
        And I name it "Christmas sale"
        And I add the "Item percentage discount" action configured without a percentage value
        And I try to add it
        Then I should be notified that this value should not be blank
        And promotion with name "Christmas sale" should not be added

    @ui @javascript
    Scenario: Trying to add a new promotion with a wrong order percentage discount
        When I want to create a new promotion
        And I specify its code as "christmas_sale"
        And I name it "Christmas sale"
        And I add the "Order percentage discount" action configured with a percentage value of 120%
        And I try to add it
        Then I should be notified that a percentage discount value must be between 0% and 100%
        And promotion with name "Christmas sale" should not be added

    @ui @javascript
    Scenario: Trying to add a new promotion with a wrong item percentage discount
        When I want to create a new promotion
        And I specify its code as "christmas_sale"
        And I name it "Christmas sale"
        And I add the "Item percentage discount" action configured with a percentage value of 130%
        And I try to add it
        Then I should be notified that a percentage discount value must be between 0% and 100%
        And promotion with name "Christmas sale" should not be added
