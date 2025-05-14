# Discount Plugin

A comprehensive discount management system for the Travel Management System, allowing for flexible discount application based on various criteria.

## Features

- Multiple discount types (percentage, fixed amount, free nights, etc.)
- Targeting specific hotels, room types, or board types
- Discount codes (promo codes)
- Stackable and exclusive discounts
- Various condition types for complex discount rules
- Usage tracking and validation

## Discount Types

- **Percentage**: Apply a percentage discount to the total price
- **Fixed Amount**: Apply a fixed amount discount to the total price
- **Free Nights**: Apply a specific number of free nights
- **Nth Night Free**: Every Nth night is free (e.g., every 3rd night)
- **Early Booking**: Apply a discount for reservations made in advance
- **Last Minute**: Apply a discount for last-minute reservations
- **Long Stay**: Apply a discount for longer stays
- **Package Deal**: Apply a discount when certain services are booked together

## Targeting

Discounts can be applied to:
- All items
- Specific hotels
- Specific room types
- Specific board types

Room types can be filtered by hotel for easier selection.

## Conditions

Various conditions can be applied to discounts:
- Minimum stay length
- Specific days of the week
- Check-in days
- Check-out days
- Minimum/maximum guests
- Minimum/maximum advance booking days
- And more

## Usage

Create discounts through the admin panel:
- Create from scratch: `/admin/discounts/create`
- Create from preset templates: `/admin/preset-discounts`

To apply discounts in code:

```php
// Find applicable discounts
$discounts = $discountService->findApplicableDiscounts('hotel', $hotelId, [
    'check_in' => $checkIn,
    'check_out' => $checkOut,
    'guests' => $adults + $children,
]);

// Apply best discount
[$discountedPrice, $appliedDiscount] = $discountService->applyBestDiscount(
    $discounts,
    $originalPrice,
    ['nights' => $nights]
);

// Or apply stackable discounts
[$discountedPrice, $appliedDiscounts] = $discountService->applyStackableDiscounts(
    $discounts,
    $originalPrice,
    ['nights' => $nights]
);

// Record usage
$discountService->recordUsage(
    $discount,
    'reservation',
    $reservation->id,
    $discountAmount,
    $userId,
    $discountCode
);
```

## Integration

The Discount plugin integrates with:
- Booking system for applying discounts to reservations
- Pricing service for correctly calculating rates with discounts
- API for validating and applying discount codes