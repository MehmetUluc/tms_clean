# Booking Wizard To-Do List

## 📊 Current System Status

### ✅ Working Features
1. **Per room/per person pricing** - Correctly implemented
   - `pricing_calculation_method` field respected
   - `is_per_person` flag in daily_rates working
   - UI displays prices correctly based on method

2. **Refundable/Non-refundable** - Partially working
   - Discount calculation implemented
   - UI shows refundable status
   - Missing: Hotel-level policy enforcement

3. **Child policy infrastructure** - Database ready
   - All necessary columns exist
   - Models configured with fields
   - Missing: Integration with pricing

### ❌ Not Working/Missing Features
1. **Child policy price calculation** - Not implemented
2. **Inventory control** - Using dummy data
3. **Existing reservation check** - No overbooking prevention
4. **Hotel-level refund policy** - Not enforced
5. **SOR/SAT (Sale on Request/Stop Sale)** - Not implemented

## 📅 Implementation Plan

### Phase 1: Price Calculation Service
- [ ] Implement child policy in price calculations
  - [ ] Free children under age limit
  - [ ] Discounted rates for children
  - [ ] Maximum children per room validation
- [ ] Age-based pricing logic
- [ ] Adult + children total price calculation

### Phase 2: Inventory Management
- [ ] Real-time availability checking
  - [ ] Query existing reservations
  - [ ] Calculate available rooms
  - [ ] Prevent overbooking
- [ ] Implement inventory tracking
  - [ ] Track allocations per date
  - [ ] Update availability after booking

### Phase 3: SOR/SAT Implementation
- [ ] **SOR (Sale on Request)**
  - [ ] Add `is_on_request` field to daily_rates
  - [ ] Show "On Request" status in UI
  - [ ] Different booking flow for SOR
  - [ ] Admin approval workflow
- [ ] **SAT (Stop Sale)**
  - [ ] Add `stop_sale` field to daily_rates
  - [ ] Block bookings when stop_sale = true
  - [ ] Admin interface to manage stop sales
  - [ ] Automatic stop sale when inventory = 0

### Phase 4: Refund Policy Enhancement
- [ ] Enforce hotel-level policies
  - [ ] Check `allow_refundable` flag
  - [ ] Check `allow_non_refundable` flag
  - [ ] Apply `non_refundable_discount` correctly
- [ ] Rate plan level enforcement
- [ ] Cancellation policy integration

### Phase 5: Testing & Validation
- [ ] Test scenarios:
  - [ ] 2 adults + 1 child (free)
  - [ ] 2 adults + 2 children (1 paid)
  - [ ] Non-refundable discount verification
  - [ ] Full booked room checking
  - [ ] Stop sale blocking
  - [ ] On request flow
  - [ ] Overbooking prevention
  - [ ] Per person vs per room pricing

## 🗄️ Database Changes Needed

### daily_rates table
```sql
ALTER TABLE daily_rates 
ADD COLUMN is_on_request BOOLEAN DEFAULT FALSE,
ADD COLUMN stop_sale BOOLEAN DEFAULT FALSE,
ADD COLUMN stop_sale_date TIMESTAMP NULL,
ADD COLUMN on_request_deadline INTEGER DEFAULT 24; -- hours before check-in
```

### inventories table (if not exists)
```sql
CREATE TABLE inventories (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    room_id BIGINT NOT NULL,
    date DATE NOT NULL,
    total_inventory INT NOT NULL,
    allocated INT DEFAULT 0,
    available INT GENERATED ALWAYS AS (total_inventory - allocated),
    stop_sale BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY unique_room_date (room_id, date),
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);
```

## 🔧 Service Methods to Implement

### PricingService
```php
- calculateChildPrice($ratePlan, $childAge, $childNumber)
- applyChildPolicy($basePrice, $hotel, $room, $children)
- validateChildrenCount($room, $childrenCount)
```

### InventoryService
```php
- checkAvailability($roomId, $checkIn, $checkOut)
- getAvailableRooms($hotelId, $checkIn, $checkOut)
- reserveInventory($roomId, $checkIn, $checkOut, $quantity)
- releaseInventory($reservationId)
```

### SORSATService
```php
- isOnRequest($roomId, $date)
- isStopSale($roomId, $date)
- setStopSale($roomId, $dates, $enabled)
- setOnRequest($roomId, $dates, $enabled)
- canBookOnRequest($roomId, $checkIn)
```

## 🎯 Success Criteria
1. Child policies correctly calculate prices
2. No overbooking possible
3. Stop sale prevents bookings
4. On request shows different UI/flow
5. Refund policies enforced at all levels
6. All test scenarios pass
7. Performance acceptable (< 2s for availability check)

## 📝 Notes
- Consider caching for frequently accessed inventory data
- Add background job for inventory cleanup
- Implement audit trail for inventory changes
- Consider rate limiting for availability checks
- Add webhook notifications for SOR bookings