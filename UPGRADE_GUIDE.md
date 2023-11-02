# Upgrade guide

TODO: review and provide examples.

## Gotchas moving from OBP to CBP

### Useful links

* [Pagination](https://developer.zendesk.com/api-reference/introduction/pagination)
* [Ticketing sorting](https://developer.zendesk.com/api-reference/ticketing/tickets/tickets/#sorting)

### CBP ordering

When moving from OBP to CBP sorting params change as well:

* From: `?sort_name=updated_at&sort_order=desc`
* To: `sort=-updated_at`

Ticket example:

* OBP: https://{subdomain}.zendesk.com/api/v2/tickets?sort_order=desc&sort_by=updated_at&per_page=2
* CBP: https://{subdomain}.zendesk.com/api/v2/tickets?sort=-updated_at&page[size]=2

However, the list of attributes you can sort by is longer in OBP:

https://developer.zendesk.com/api-reference/ticketing/tickets/tickets/#sorting

* OBP: `"assignee", "assignee.name", "created_at", "group", "id", "locale", "requester"`
* CBP: `"updated_at", "id", "status"`

Example:

* OBP: https://{subdomain}.zendesk.com/api/v2/tickets?sort_order=desc&sort_by=assignee.name&per_page=2 HTTP 200, works
* CBP: https://{subdomain}.zendesk.com/api/v2/tickets?sort=assignee.name&page[size]=2 HTTP 400

```json
{
    "error": "InvalidPaginationParameter",
    "description": "sort is not valid"
}
```

TODO: confirm.
If this is your situation, you need to change sorting order to a supported one.

###
