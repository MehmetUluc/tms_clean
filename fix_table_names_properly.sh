#!/bin/bash

cd /var/www/app/Plugins/Partner/Models/

# Fix table names in model files correctly
sed -i "s/protected \$table = 'partnerministryreports'/protected \$table = 'partner_ministry_reports'/g" PartnerMinistryReport.php
sed -i "s/protected \$table = 'partnerpayments'/protected \$table = 'partner_payments'/g" PartnerPayment.php
sed -i "s/protected \$table = 'partnerpaymentrequests'/protected \$table = 'partner_payment_requests'/g" PartnerPaymentRequest.php
sed -i "s/protected \$table = 'partnertransactions'/protected \$table = 'partner_transactions'/g" PartnerTransaction.php

echo "Table names properly updated!"