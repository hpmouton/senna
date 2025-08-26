<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    /**
     * Get all of the transactions for the user through their accounts.
     */
    public function transactions(): HasManyThrough
    {
        return $this->hasManyThrough(Transaction::class, Account::class);
    }

     public function seedDefaultCategories(): void
    {
        $categories = [
            'Income' => ['Salary', 'Bonus', 'Freelance'],
            'Housing' => ['Rent/Mortgage', 'Utilities', 'Maintenance'],
            'Food' => ['Groceries', 'Restaurants', 'Coffee Shops'],
            'Transportation' => ['Gas/Fuel', 'Public Transit', 'Ride Sharing'],
            'Personal Care' => ['Haircut', 'Toiletries', 'Subscriptions'],
            'Entertainment' => ['Movies', 'Concerts', 'Hobbies'],
        ];

        foreach ($categories as $parentName => $children) {
            // Create the parent category
            $parent = $this->categories()->create([
                'name' => $parentName,
                'parent_id' => null,
            ]);

            // Create the child categories
            foreach ($children as $childName) {
                $this->categories()->create([
                    'name' => $childName,
                    'parent_id' => $parent->id,
                ]);
            }
        }
    }
}
