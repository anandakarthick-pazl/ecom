# POS Discount & Tax Features - Quick Reference Guide

## 🛒 Using Item-Level Discounts

### Step-by-Step Instructions:

1. **Add items to cart** by clicking on product cards
2. **Apply item discount:**
   - Click the **%** button next to any cart item
   - Enter discount **amount** (in ₹) OR **percentage** (%)
   - System automatically calculates the other value
   - Preview shows the net amount
   - Click **"Apply Discount"**

### Discount Options:
- **Amount Discount:** Enter ₹50 to give ₹50 off the item
- **Percentage Discount:** Enter 10% to give 10% off the item
- **Real-time Preview:** See net amount before applying

## 💰 Tax Management

### Auto Tax Mode (Default):
- System calculates tax based on product tax percentages
- Tax applied on net amount (after item discounts)
- Shows CGST and SGST separately

### Manual Tax Mode:
1. Toggle the **"Manual"** switch next to Tax field
2. Enter custom tax amount
3. Add tax notes if needed
4. System uses your entered amount instead of auto-calculation

## 🧾 Sale-Level Discounts

### Additional Discounts:
- Use the **"Discount"** field in cart summary
- This is applied AFTER item-level discounts
- Good for customer loyalty discounts or promotional offers

## 📊 Understanding the Calculations

### Calculation Flow:
```
Item Price × Quantity = Gross Amount
Gross Amount - Item Discount = Net Amount
Net Amount × Tax% = Tax Amount
Net Amount + Tax - Sale Discount = Final Amount
```

### Example:
```
Product: ₹100 × 2 = ₹200 (Gross)
Item Discount: ₹20 = ₹180 (Net)
Tax (18%): ₹32.40 = ₹212.40 (With Tax)
Sale Discount: ₹10 = ₹202.40 (Final)
```

## 🎯 Best Practices

### For Item Discounts:
- Use **percentage discounts** for promotional offers (10% off)
- Use **amount discounts** for fixed reductions (₹50 off)
- Apply to individual items for specific product promotions

### For Sale Discounts:
- Use for customer loyalty programs
- Apply for bulk purchase discounts
- Use for special occasion offers

### For Tax Management:
- Use **auto mode** for standard sales
- Use **manual mode** for special tax situations
- Always add notes when using manual tax

## 🔧 Troubleshooting

### Common Issues:

**Discount too high:**
- System prevents discounts exceeding item value
- Check discount amount vs. item total

**Tax not calculating:**
- Ensure products have tax percentage set
- Check if manual tax mode is enabled

**Calculations seem wrong:**
- Remember: Tax calculated AFTER item discounts
- Sale discount applied at the end

## 📱 Interface Tips

### Cart Display:
- **Green text** = Discount amount
- **Bold total** = Net amount after discount
- **% button** = Edit item discount
- **Trash button** = Remove item

### Summary Section:
- **Items:** Total quantity
- **Subtotal:** Net total after item discounts
- **Discount:** Additional sale-level discount
- **CGST/SGST:** Split tax amounts
- **Total:** Final amount to collect

## 🎉 Quick Start Checklist

- [ ] Add products to cart
- [ ] Apply item discounts where needed
- [ ] Set sale-level discount if applicable
- [ ] Choose tax mode (auto/manual)
- [ ] Verify total amount
- [ ] Select payment method
- [ ] Complete sale

## 💡 Pro Tips

1. **Batch Discounts:** Apply same percentage to multiple items
2. **Customer Training:** Show customers the discount breakdown
3. **Reporting:** All discounts are tracked for analysis
4. **Speed:** Use keyboard shortcuts for quick entry
5. **Validation:** System prevents over-discounting automatically

---

## Support

For technical issues or questions:
- Check the main documentation: `POS_DISCOUNT_TAX_ENHANCEMENT_SUMMARY.md`
- Verify database setup with migration scripts
- Test in a development environment first

**Happy Selling! 🚀**
