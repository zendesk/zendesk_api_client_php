# OBP to CBP Upgrade guide

## Useful links

- [This README](./README.md#pagination)
- [Pagination](https://developer.zendesk.com/api-reference/introduction/pagination)
- [Ticketing sorting](https://developer.zendesk.com/api-reference/ticketing/tickets/tickets/#sorting)

## CBP Basics

**OBP (Offset Based Pagination)** is quite inefficient, and increasingly so the higher the page you fetch. Switching to **CBP (Cursor Based Pagination)** will improve your application performance. OBP will eventually be subject to limits.

When using OBP, if you request page 100, the DB will need to index all records that proceed it before it can load the rows for the page you requested.

CBP works based on cursors, so when ordering by `id` with page size 10, if the last item on your page has id 111, the response includes a link to the next page (`links.next`) which can be used to request the next 10 elements after id 111, and only the requested rows are indexed before fetching. When in the response `meta.has_more` is `false` you are done.

## The new iterator

The best way to upgrade your app to support CBP is to use the iterator we provide. Please refer to the [README](./README.md#iterator-recommended).

**Note**: The iterator will automatically convert the param `page_size` to work with CBP.

The iterator is available in `v3.0`, which you can download with:

```sh
composer update zendesk/zendesk_api_client_php
```

## Debugging

Please refer to the [README](./README.md#debugging).

## API calls

Note the query parameters change in these two URL examples:

- OBP: https://{subdomain}.zendesk.com/api/v2/tickets?sort_order=desc&sort_by=updated_at&per_page=2
- CBP: https://{subdomain}.zendesk.com/api/v2/tickets?sort=-updated_at&page[size]=2

### CBP ordering

When moving from OBP to CBP sorting params _may_ change as well:

- From: `?sort_name=updated_at&sort_order=desc`
- To: `sort=-updated_at`

However, the list of **attributes you can sort by might also change** with the pagination type:

https://developer.zendesk.com/api-reference/ticketing/tickets/tickets/#sorting

- OBP: `"assignee", "assignee.name", "created_at", "group", "id", "locale", "requester"`
- CBP: `"updated_at", "id", "status"`

Example:

- OBP: https://{subdomain}.zendesk.com/api/v2/tickets?sort_order=desc&sort_by=assignee.name&per_page=2 `HTTP 200`, works
- CBP: https://{subdomain}.zendesk.com/api/v2/tickets?sort=assignee.name&page[size]=2 `HTTP 400`

```json
{
  "error": "InvalidPaginationParameter",
  "description": "sort is not valid"
}
```

If this is your situation, **you will need to change the sorting order** to a supported one.

## Parallel requests

If you are fetching multiple pages in parallel using OBP, you need to refactor to a serial execution, and fetch one page at a time.
