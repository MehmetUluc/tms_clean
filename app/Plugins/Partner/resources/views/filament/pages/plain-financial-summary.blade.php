<x-filament::page>
    <h1>Plain Financial Summary</h1>
    <p>This is a plain version with minimal styling</p>
    
    <div style="margin-top: 20px; border: 1px solid blue; padding: 15px; border-radius: 5px;">
        <h2>Current Vendor Information</h2>
        <ul>
            <li>Vendor ID: {{ $this->vendor->id }}</li>
            <li>Vendor Name: {{ $this->vendor->name }}</li>
            <li>Status: {{ $this->vendor->status }}</li>
        </ul>
    </div>
    
    <div style="margin-top: 20px; display: flex; gap: 10px;">
        <div style="border: 1px solid green; padding: 15px; border-radius: 5px; flex: 1;">
            <h3>Total Revenue</h3>
            <p style="font-size: 24px; font-weight: bold;">₺1,254,890</p>
        </div>
        
        <div style="border: 1px solid orange; padding: 15px; border-radius: 5px; flex: 1;">
            <h3>Occupancy Rate</h3>
            <p style="font-size: 24px; font-weight: bold;">76.4%</p>
        </div>
        
        <div style="border: 1px solid red; padding: 15px; border-radius: 5px; flex: 1;">
            <h3>Reservations</h3>
            <p style="font-size: 24px; font-weight: bold;">1,284</p>
        </div>
    </div>
    
    <div style="margin-top: 20px; border: 1px solid #ccc; padding: 15px; border-radius: 5px;">
        <h2>Transaction History</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f3f4f6;">
                    <th style="padding: 8px; text-align: left; border-bottom: 1px solid #ddd;">Date</th>
                    <th style="padding: 8px; text-align: left; border-bottom: 1px solid #ddd;">Reference</th>
                    <th style="padding: 8px; text-align: left; border-bottom: 1px solid #ddd;">Type</th>
                    <th style="padding: 8px; text-align: left; border-bottom: 1px solid #ddd;">Amount</th>
                    <th style="padding: 8px; text-align: left; border-bottom: 1px solid #ddd;">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">2025-05-10</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">TRX-12345</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Booking</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">₺12,450</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Processed</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">2025-05-08</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">TRX-12344</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Booking</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">₺9,850</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Processed</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">2025-05-07</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">TRX-12343</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Cancellation</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">-₺4,200</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Processed</td>
                </tr>
            </tbody>
        </table>
    </div>
</x-filament::page>