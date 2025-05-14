#!/bin/bash

cd /var/www/app/Plugins/Partner/Models/

# Update remaining model files
models=("PartnerMinistryReport.php" "PartnerPayment.php" "PartnerPaymentRequest.php" "PartnerTransaction.php")

for model in "${models[@]}"; do
  # Replace App\Plugins\Vendor\Models with App\Plugins\Partner\Models
  sed -i 's/namespace App\\Plugins\\Vendor\\Models;/namespace App\\Plugins\\Partner\\Models;/g' "$model"
  
  # Get the model name without .php extension
  filename=$(basename "$model" .php)
  # Get original class name by removing "Partner" prefix
  original_class=${filename#Partner}
  original_class="Vendor$original_class"
  # Replace class name (VendorXXX with PartnerXXX)
  sed -i "s/class $original_class /class $filename /g" "$model"
  
  # Replace vendor_ with partner_ in table names
  original_table=${original_class,,}s # Convert to lowercase and add s
  new_table=${filename,,}s # Convert to lowercase and add s
  sed -i "s/protected \$table = '$original_table'/protected \$table = '$new_table'/g" "$model"
  
  # Replace vendor_id with partner_id in fillable arrays
  sed -i "s/'vendor_id'/'partner_id'/g" "$model"
  
  # Replace Vendor model references with Partner
  sed -i 's/Vendor::class/Partner::class/g' "$model"
  
  # Replace vendor() method with partner()
  sed -i 's/public function vendor()/public function partner()/g' "$model"
  sed -i 's/return \$this->belongsTo(Vendor::class)/return \$this->belongsTo(Partner::class)/g' "$model"
  
  # Replace VendorXXX::class with PartnerXXX::class in relationship methods
  for other_model in "${models[@]}"; do
    other_filename=$(basename "$other_model" .php)
    other_original_class=${other_filename#Partner}
    other_original_class="Vendor$other_original_class"
    sed -i "s/$other_original_class::class/$other_filename::class/g" "$model"
  done
  
  # Replace specific relationships from vendor model
  sed -i 's/VendorBankAccount::class/PartnerBankAccount::class/g' "$model"
  sed -i 's/VendorDocument::class/PartnerDocument::class/g' "$model"
  sed -i 's/VendorCommission::class/PartnerCommission::class/g' "$model"
  
  echo "Updated $model"
done

echo "All model files updated successfully!"