    <?php

    namespace App\Models;

    // use App\Models\BaseClass\Item;
    use App\DTOs\Item;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Support\Facades\Storage;
    use CloudinaryLabs\CloudinaryLaravel\MediaAlly;

    class Product extends BaseModel
    {
        use MediaAlly;

        private Item $item;

        protected $fillable = [
            'id',
            'name',
            'details',
            'sell_in',
            'quality',
            'external_id', // restful-api id
            'product_rule_id'
        ];

        public function __construct(array $attributes = [])
        {
            parent::__construct($attributes);

            $this->item = new Item(
                $attributes['name'] ?? '',
                $attributes['sell_in'] ?? 0,
                $attributes['quality'] ?? 0
            );
        }

        // Access the product rules
        public function rule(): BelongsTo
        {
            return $this->belongsTo(ProductRule::class, 'product_rule_id');
        }

        /**
         * Get the decoded details as an array.
         *
         * @return array|null
         */
        public function getDetailsAttribute(): ?array
        {
            return json_decode($this->attributes['details'], true);
        }

        public function getImageAttribute($value)
        {
            // Check if storage driver is 'cloudinary', adjust as necessary for other drivers
            if (Storage::disk('cloudinary')->exists($value)) {
                // todo: creating temporary.
                return Storage::disk('cloudinary')->url($value);
            }
            // Fallback to just returning null if not found or not on Cloudinary
            return null;
        }

        // Getter to access the underlying Item instance
        public function getItem(): Item
        {
            return $this->item;
        }

        // Proxy methods to interact with Item (if needed)
        public function __toString(): string
        {
            return $this->item->__toString();  // Delegate to Item's __toString
        }

        public function getImgUrl(): string
        {
            return $this->item->getImgUrl();  // Delegate to Item's getImgUrl
        }

        public function setImgUrl(string $imgUrl): self
        {
            $this->item->setImgUrl($imgUrl);
            return $this;
        }
    }
