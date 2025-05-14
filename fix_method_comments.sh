#!/bin/bash

cd /var/www/app/Plugins/Partner/Models/

# Fix method comments in model files
models=("PartnerMinistryReport.php" "PartnerPayment.php" "PartnerPaymentRequest.php" "PartnerTransaction.php" "Partner.php" "PartnerBankAccount.php" "PartnerCommission.php" "PartnerDocument.php")

for model in "${models[@]}"; do
  if [ -f "$model" ]; then
    # Replace "vendor" with "partner" in method comments
    sed -i 's/\* Get the vendor that owns/\* Get the partner that owns/g' "$model"
    sed -i 's/\* Check if vendor is/\* Check if partner is/g' "$model"
    
    echo "Fixed method comments in $model"
  else
    echo "File $model not found, skipping"
  fi
done

echo "All method comments updated successfully!"