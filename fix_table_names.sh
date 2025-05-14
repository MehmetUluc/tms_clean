#!/bin/bash

cd /var/www/app/Plugins/Partner/Models/

# Fix table names in model files
models=("PartnerMinistryReport.php" "PartnerPayment.php" "PartnerPaymentRequest.php" "PartnerTransaction.php")

for model in "${models[@]}"; do
  # Get the model name without .php extension
  filename=$(basename "$model" .php)
  # Convert to lowercase and add s for table name
  new_table=${filename,,}s
  
  # Replace vendor_ table names with partner_ table names
  sed -i "s/protected \$table = 'vendor_[^']*'/protected \$table = '$new_table'/g" "$model"
  
  echo "Fixed table name in $model"
done

echo "All table names updated successfully!"