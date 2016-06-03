#!/bin/bash

mysql -u root -p1990208@hxt < build_database.sql
mysql -u root -p1990208@hxt yumbox < test_data.sql

