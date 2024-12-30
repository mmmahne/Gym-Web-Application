# Gym Management API Documentation

I will upload the frontend later

## Base URL
`http://localhost:8000/api`

## Authentication
All API requests require authentication token except for login and register endpoints.
Include the token in request headers: 


## Endpoints

### Authentication
#### Login
- **POST** `/auth/login`
- **Body:**
  ```json
  {
    "email": "user@example.com",
    "password": "password"
  }
  ```
- **Response:**
  ```json
  {
    "data": {
      "token": "your_access_token",
      "user": {
        "id": 1,
        "name": "User Name",
        "email": "user@example.com",
        "role": "user"
      }
    },
    "message": "Login successful"
  }
  ```

#### Register
- **POST** `/auth/register`
- **Body:**
  ```json
  {
    "name": "User Name",
    "email": "user@example.com",
    "password": "password",
    "password_confirmation": "password",
    "phone": "1234567890",
    "address": "123 Street"
  }
  ```
- **Response:**
  ```json
  {
    "data": {
      "token": "your_access_token",
      "user": {
        "id": 1,
        "name": "User Name",
        "email": "user@example.com",
        "role": "user"
      }
    },
    "message": "Registration successful"
  }
  ```

#### Logout
- **POST** `/auth/logout`
- **Headers:** Required authentication token
- **Response:**
  ```json
  {
    "message": "Logged out successfully"
  }
  ```

### Profile Management
#### View Profile
- **GET** `/profile`
- **Headers:** Required authentication token
- **Response:**
  ```json
  {
    "data": {
      "id": 1,
      "name": "User Name",
      "email": "user@example.com",
      "phone": "1234567890",
      "address": "123 Street",
      "role": "user"
    }
  }
  ```

#### Update Profile
- **PUT** `/profile`
- **Headers:** Required authentication token
- **Body:**
  ```json
  {
    "name": "Updated Name",
    "phone": "0987654321",
    "address": "456 Avenue"
  }
  ```
- **Response:**
  ```json
  {
    "data": {
      "id": 1,
      "name": "Updated Name",
      "email": "user@example.com",
      "phone": "0987654321",
      "address": "456 Avenue"
    },
    "message": "Profile updated successfully"
  }
  ```

#### Update Password
- **PUT** `/profile/password`
- **Headers:** Required authentication token
- **Body:**
  ```json
  {
    "current_password": "old_password",
    "password": "new_password",
    "password_confirmation": "new_password"
  }
  ```
- **Response:**
  ```json
  {
    "message": "Password updated successfully"
  }
  ```

### Gym Classes
#### List All Classes
- **GET** `/classes`
- **Headers:** Required authentication token
- **Query Parameters:**
  - `date` (optional): Filter by date (YYYY-MM-DD)
  - `type` (optional): Filter by class type
- **Response:**
  ```json
  {
    "data": [
      {
        "id": 1,
        "name": "Yoga Class",
        "description": "Beginner friendly yoga class",
        "type": "yoga",
        "trainer_id": 2,
        "max_capacity": 20,
        "trainer": {
          "id": 2,
          "name": "Trainer Name"
        }
      }
    ]
  }
  ```

#### Get Class Schedule
- **GET** `/classes/schedule`
- **Headers:** Required authentication token
- **Query Parameters:**
  - `from_date` (optional): Start date (YYYY-MM-DD)
  - `to_date` (optional): End date (YYYY-MM-DD)
- **Response:**
  ```json
  {
    "data": [
      {
        "date": "2024-03-21",
        "classes": [
          {
            "id": 1,
            "name": "Yoga Class",
            "time": "09:00:00",
            "available_slots": 15
          }
        ]
      }
    ]
  }
  ```

#### Create Class (Trainer/Admin only)
- **POST** `/classes`
- **Headers:** Required authentication token
- **Body:**
  ```json
  {
    "name": "New Yoga Class",
    "description": "Description of the class",
    "type": "yoga",
    "max_capacity": 20,
    "schedule": [
      {
        "day": "monday",
        "time": "09:00",
        "duration": 60
      }
    ]
  }
  ```
- **Response:**
  ```json
  {
    "data": {
      "id": 1,
      "name": "New Yoga Class",
      "type": "yoga",
      "max_capacity": 20
    },
    "message": "Class created successfully"
  }
  ```

### Bookings
#### List User's Bookings
- **GET** `/bookings/my`
- **Headers:** Required authentication token
- **Query Parameters:**
  - `status` (optional): Filter by status (confirmed, cancelled, completed)
- **Response:**
  ```json
  {
    "data": [
      {
        "id": 1,
        "class": {
          "id": 1,
          "name": "Yoga Class",
          "type": "yoga"
        },
        "booking_date": "2024-03-21",
        "status": "confirmed",
        "attended": false
      }
    ]
  }
  ```

#### Book a Class
- **POST** `/bookings`
- **Headers:** Required authentication token
- **Body:**
  ```json
  {
    "class_id": 1,
    "booking_date": "2024-03-21"
  }
  ```
- **Response:**
  ```json
  {
    "data": {
      "id": 1,
      "class": {
        "name": "Yoga Class",
        "type": "yoga"
      },
      "booking_date": "2024-03-21",
      "status": "confirmed"
    },
    "message": "Booking confirmed successfully"
  }
  ```

#### Cancel Booking
- **POST** `/bookings/{booking_id}/cancel`
- **Headers:** Required authentication token
- **Response:**
  ```json
  {
    "data": {
      "id": 1,
      "status": "cancelled"
    },
    "message": "Booking cancelled successfully"
  }
  ```

#### View Booking Details
- **GET** `/bookings/{booking_id}`
- **Headers:** Required authentication token
- **Response:**
  ```json
  {
    "data": {
      "id": 1,
      "class": {
        "id": 1,
        "name": "Yoga Class",
        "type": "yoga",
        "trainer": {
          "name": "Trainer Name"
        }
      },
      "booking_date": "2024-03-21",
      "status": "confirmed",
      "attended": false
    }
  }
  ```

### Memberships
#### List All Memberships (Admin only)
- **GET** `/memberships`
- **Headers:** Required authentication token
- **Response:**
  ```json
  {
    "data": [
      {
        "id": 1,
        "user": {
          "id": 1,
          "name": "User Name"
        },
        "start_date": "2024-03-01",
        "end_date": "2024-04-01",
        "type": "monthly",
        "is_active": true,
        "payment_status": "paid"
      }
    ]
  }
  ```

#### Get Active Membership
- **GET** `/memberships/active`
- **Headers:** Required authentication token
- **Response:**
  ```json
  {
    "data": {
      "id": 1,
      "start_date": "2024-03-01",
      "end_date": "2024-04-01",
      "type": "monthly",
      "is_active": true,
      "payment_status": "paid"
    }
  }
  ```

#### Create Membership (Admin only)
- **POST** `/memberships`
- **Headers:** Required authentication token
- **Body:**
  ```json
  {
    "user_id": 1,
    "type": "monthly",
    "start_date": "2024-03-01",
    "end_date": "2024-04-01",
    "payment_status": "paid"
  }
  ```
- **Response:**
  ```json
  {
    "data": {
      "id": 1,
      "user_id": 1,
      "type": "monthly",
      "start_date": "2024-03-01",
      "end_date": "2024-04-01",
      "is_active": true
    },
    "message": "Membership created successfully"
  }
  ```

### Check-ins
#### User Check-in
- **POST** `/check-in`
- **Headers:** Required authentication token
- **Response:**
  ```json
  {
    "data": {
      "membership": {
        "id": 1,
        "type": "monthly",
        "end_date": "2024-04-01"
      },
      "check_in": {
        "id": 1,
        "user_id": 1,
        "check_in_time": "2024-03-21 09:00:00",
        "status": "completed"
      }
    },
    "message": "Check-in successful"
  }
  ```

#### Check-in to Class
- **POST** `/check-in/class/{booking_id}`
- **Headers:** Required authentication token
- **Response:**
  ```json
  {
    "data": {
      "id": 1,
      "class": {
        "name": "Yoga Class"
      },
      "check_in_time": "2024-03-21 09:00:00",
      "status": "completed"
    },
    "message": "Class check-in successful"
  }
  ```

#### View Check-in History (Admin only)
- **GET** `/check-in/history`
- **Headers:** Required authentication token
- **Query Parameters:**
  - `from_date` (optional): Filter from date
  - `to_date` (optional): Filter to date
  - `user_id` (optional): Filter by user
- **Response:**
  ```json
  {
    "data": [
      {
        "id": 1,
        "user": {
          "id": 1,
          "name": "User Name"
        },
        "check_in_time": "2024-03-21 09:00:00",
        "class": null,
        "status": "completed"
      }
    ]
  }
  ```

### Attendance
#### Mark Attendance (Trainer/Admin only)
- **POST** `/attendance/bookings/{booking_id}`
- **Headers:** Required authentication token
- **Body:**
  ```json
  {
    "attended": true
  }
  ```
- **Response:**
  ```json
  {
    "data": {
      "id": 1,
      "user": {
        "name": "User Name"
      },
      "class": {
        "name": "Yoga Class"
      },
      "attended": true
    },
    "message": "Attendance marked successfully"
  }
  ```

#### Get Class Attendance (Trainer/Admin only)
- **GET** `/attendance/class`
- **Query Parameters:**
  - `class_id`: Required class ID
  - `date`: Required date (YYYY-MM-DD)
- **Response:**
  ```json
  {
    "data": {
      "class": {
        "id": 1,
        "name": "Yoga Class"
      },
      "date": "2024-03-21",
      "attendees": [
        {
          "user_id": 1,
          "name": "User Name",
          "attended": true,
          "check_in_time": "2024-03-21 09:00:00"
        }
      ]
    }
  }
  ```

#### View My Attendance (User)
- **GET** `/attendance/my`
- **Query Parameters:**
  - `from_date` (optional): Start date
  - `to_date` (optional): End date
- **Response:**
  ```json
  {
    "data": [
      {
        "date": "2024-03-21",
        "class": {
          "name": "Yoga Class"
        },
        "attended": true,
        "check_in_time": "2024-03-21 09:00:00"
      }
    ]
  }
  ```

### Statistics
#### Dashboard Stats (Admin only)
- **GET** `/stats/dashboard`
- **Headers:** Required authentication token
- **Response:**
  ```json
  {
    "data": {
      "total_users": 150,
      "active_memberships": 120,
      "today_check_ins": 45,
      "today_classes": 8,
      "monthly_stats": {
        "new_users": 20,
        "class_attendance": 350,
        "check_ins": 800
      }
    }
  }
  ```

#### User Stats
- **GET** `/stats/user`
- **Headers:** Required authentication token
- **Response:**
  ```json
  {
    "data": {
      "total_classes_attended": 25,
      "total_check_ins": 45,
      "current_streak": 5,
      "monthly_attendance": 15,
      "membership_status": {
        "is_active": true,
        "expires_in": "20 days"
      }
    }
  }
  ```

### Error Handling
All error responses follow this format:
```json
{
  "message": "Error message here",
  "errors": {
    "field_name": [
      "Validation error message"
    ]
  }
}
```

Common HTTP Status Codes:
- `200`: Success
- `201`: Created
- `400`: Bad Request
- `401`: Unauthorized
- `403`: Forbidden
- `404`: Not Found
- `422`: Validation Error
- `500`: Server Error

### Common Response Format
All successful responses follow this format:
```json
{
  "data": {
    // Response data here
  },
  "message": "Success message (optional)"
}
```

For paginated responses:
```json
{
  "data": [
    // Array of items
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 50,
    "total_pages": 4
  },
  "links": {
    "first": "http://api.example.com/items?page=1",
    "last": "http://api.example.com/items?page=4",
    "prev": null,
    "next": "http://api.example.com/items?page=2"
  }
}
```

### Authentication Headers
For protected routes, include the token in the Authorization header:
```
Authorization: Bearer your_access_token_here
```

### Rate Limiting
The API implements rate limiting to prevent abuse:
- 60 requests per minute for authenticated users
- 30 requests per minute for unauthenticated users

Rate limit headers in response:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1616774400
```

### File Upload
For endpoints that accept file uploads:
- Maximum file size: 5MB
- Supported formats: jpg, jpeg, png
- Use multipart/form-data content type

### Development Setup
1. Clone the repository
2. Copy `.env.example` to `.env`
3. Configure database settings in `.env`
4. Run:
   ```bash
   composer install
   php artisan key:generate
   php artisan migrate
   php artisan db:seed
   ```

### Testing
Run the test suite:
```bash
php artisan test
```

Or run specific test:
```bash
php artisan test --filter=TestName
```

  
