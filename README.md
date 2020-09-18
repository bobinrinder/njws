# NJWS
Training project

## Docker Setup

**Step 0:**
Install latest Docker and yarn, if you don't have it yet.

**Step 1:**
Clone this repo with `git clone git@github.com:bobinrinder/njws.git`

**Step 2:**
Enter the folder with `cd njws`

**Step 3:**
Setup the environment via `yarn docker-init` (very first time might take a bit).

This will:

-   yarn
-   copy the laravel env file from the example
-   install [laradock](http://laradock.io/) as a submodule
-   copy the laradock env file from the example
-   start the docker containers
-   run composer install
-   migrate
-   seed

**Step 4:**
That's it, your environment should be running at `http://localhost/`.
phpMyAdmin is at `http://localhost:8082` with `server: mysql, username: default, password: secret`.

Other Docker commands:

-   `yarn docker-start`: Start the containers
-   `yarn docker-stop`: Stop the containers
-   `yarn docker-test`: Migrate fresh and run tests in the container
-   `yarn docker-status`: Show the running containers
-   `yarn docker-bash`: Open the command line bash
-   `yarn docker-migrate`: Migrate tables
-   `yarn docker-fresh`: Migrate [fresh](https://laravel.com/docs/7.x/migrations#rolling-back-migrations)
-   `yarn docker-fresh-composer`: Run composer and then migrate [fresh](https://laravel.com/docs/7.x/migrations#rolling-back-migrations)
-   `yarn docker-refresh`: Migrate [refresh](https://laravel.com/docs/7.x/migrations#rolling-back-migrations)
-   `yarn docker-delete`: Delete the containers

## Usage

To trigger the handling of the task (`GET`) call `http://localhost/api/tasks/handle`.
This should:

-   fetch items from warehouses
-   fetch orders from order systems
-   verify and save processing result of all orders
-   submit processing results back to order systems
-   return json with a boolean `success` property and the processed `orders`

## Notes

Due to time constraints no implementation of:

-   UI
-   authentication
-   authorization
-   queueing / chunking
-   task handling to avoid overlapping processing
-   advanced error and exception handling
-   unit tests
-   setup of cronjob and/or scheduler

# Original Readme

A PHP project to integrate with the external ordering system and external warehouse system using REST API.

## Background of the task

This integration is the first step towards full integration with multiple warehouses and ordering systems. We would like to build a reliable system that integrates with multiple, external ordering systems and be able to evaluate those orders against available inventory in our warehouse(s) and determine if the orders can be accepted based on the requested ship date contained in the orders. We want to fetch the item availability from the warehouse system and orders from the ordering system several times per day.

## Requirements

The goal of the work sample is to create an integration with WAREHOUSE_X warehouse system and ORDERING_Y ordering system.

Fetch all orders with status "new" from the API. If the order is valid, update the order status to "processed". If the order is not valid, the order status is set to "failed".

The order is valid if the order item quantities are satisfied with warehouse item quantities and if our warehouse items are available (`availableFromDate`) before the planned order shipping date (`shippingDate`).

## External systems

### External warehouse system (WAREHOUSE_X)

The external warehouse system has provided us with the following publicly available endpoint:

1. GET https://5f591c568040620016ab8de2.mockapi.io/api/v1/warehouse-items

### External ordering system (ORDERING_Y)

The external ordering system has provided us with the following publicly available endpoints:

1. GET https://5f591c568040620016ab8de2.mockapi.io/api/v1/orders
2. PUT https://5f591c568040620016ab8de2.mockapi.io/api/v1/orders/:orderId
