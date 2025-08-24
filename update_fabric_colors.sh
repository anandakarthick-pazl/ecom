#!/bin/bash

echo "======================================"
echo "Updating all fabric files to green color"
echo "======================================"

# List of files to check
FILES=(
    "category-fabric.blade.php"
    "checkout-fabric.blade.php"
    "home-fabric.blade.php"
    "offer-products-fabric.blade.php"
    "search-fabric.blade.php"
    "track-order-fabric.blade.php"
)

# Colors to replace
OLD_COLORS=("#ff6b35" "#ff5722" "#ffd93d")
NEW_COLOR="#28a745"

for file in "${FILES[@]}"; do
    echo "Checking $file..."
    FILE_PATH="D:/source_code/ecom/resources/views/$file"
    
    if [ -f "$FILE_PATH" ]; then
        for color in "${OLD_COLORS[@]}"; do
            # Count occurrences
            COUNT=$(grep -o "$color" "$FILE_PATH" 2>/dev/null | wc -l)
            if [ $COUNT -gt 0 ]; then
                echo "  Found $COUNT instances of $color"
                # Replace the color
                sed -i "s/$color/$NEW_COLOR/gi" "$FILE_PATH"
                echo "  ✅ Replaced with $NEW_COLOR"
            fi
        done
    else
        echo "  ⚠️ File not found"
    fi
done

echo ""
echo "======================================"
echo "✅ All fabric files updated to green!"
echo "======================================