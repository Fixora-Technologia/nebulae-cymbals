# Nebulae Cymbals - Warehouse Management System

## Project Overview

Nebulae Cymbals is a Laravel-based warehouse management system designed specifically for a cymbal manufacturing company. The application provides robust inventory management, transaction tracking, and reporting capabilities. It leverages Laravel's features and follows a clean MVC architecture.

The application is divided into two main sections:
1. **Authentication frontend**: Minimal public-facing components limited to authentication (login, register, password reset).
2. **Admin backend**: Protected by authentication and authorization, allowing staff to manage inventory, products, customers, and transactions.

## Tech Stack

- **Framework**: Laravel ^10.10
- **PHP**: ^8.1
- **Frontend**: Bootstrap 5.3.3, SCSS
- **Database**: MySQL
- **Asset Management**: Vite
- **Authentication**: Laravel's built-in Auth system
- **Authorization**: Spatie Laravel Permission ^6.10
- **Activity Logging**: Spatie Laravel Activitylog ^4.9
- **Image Processing**: Intervention/Image ^3.10
- **UI Components**: 
  - Admin LTE 4.0.0-beta3
  - FontAwesome 6.7.2
  - FullCalendar 6.1.15
  - Bootstrap Icons 1.11.3
  - Quill Editor 2.0.3
  - Swiper 11.2.1

## Directory Structure

The project follows the standard Laravel directory structure with the following key components:

- **/app**: Core application code
  - **/Http/Controllers**: Application controllers for handling requests
  - **/Models**: Eloquent models representing database tables
- **/database**: Database migrations and seeders
  - **/migrations**: Database schema definitions
  - **/seeders**: Initial data seeders
- **/resources**: Frontend assets and views
  - **/views/admin**: Admin panel views
  - **/views/public**: Public-facing views
  - **/js**: JavaScript files
  - **/sass**: SCSS stylesheets
- **/routes**: Application routes
  - **/web.php**: Main web routes
  - **/api.php**: API routes
- **/config**: Application configuration files
- **/public**: Publicly accessible files
- **/storage**: Application storage (uploads, logs, etc.)

## Existing Features

### Public-Facing Features

1. **Homepage**: Displays latest news, gallery images, and testimonials
2. **News Section**: Lists and details news articles
3. **Gallery**: Image gallery with detail views
4. **Organizational Information**:
   - History page
   - Vision & Mission
   - Sectors information
   - Organizational structure and management
5. **Regulations**: List of organizational regulations
6. **Activities Calendar**: Calendar of organizational events
7. **Testimonials**: User testimonials display

### Administrative Features

1. **Dashboard**: Overview of site statistics and recent activities
2. **User Management**:
   - User CRUD operations
   - Role and permission management via Spatie Laravel Permission
3. **Member Management**: CRUD for organizational members with detailed information
4. **Content Management**:
   - News articles
   - Gallery images
   - Activities
   - Regulations
   - Testimonials
5. **Organizational Structure Management**:
   - Management positions
   - Councils
   - Sectors
   - Organizational positions
6. **Activity Logging**: Tracking of system activities and changes

## Database Schema

The database consists of several key tables:

1. **users**: User authentication information
2. **roles**: User roles (Super Admin, Kepala Gudang, Admin Gudang)
3. **permissions**: Granular permissions for actions
4. **model_has_roles**: Pivot table for user-role assignments
5. **model_has_permissions**: Pivot table for direct permission assignments
6. **role_has_permissions**: Pivot table for role-permission assignments
7. **product_categories**: Categories for products
8. **units**: Measurement units for products
9. **products**: Product information and inventory
10. **customers**: Customer information
11. **transactions**: Sales and purchase transactions
12. **transaction_items**: Line items for transactions
13. **activity_log**: System activity tracking (via Spatie Activitylog)

Key relationships include:
- Products to Product Categories (many-to-one)
- Products to Units (many-to-one)
- Transactions to Customers (many-to-one)
- Transactions to Transaction Items (one-to-many)
- Users to Roles (many-to-many)

## Application Features

### Warehouse Management
- Product category management
- Unit management
- Product inventory tracking
- Customer management
- Transaction processing (sales and purchases)
- Transaction item management

### User Management
- Role-based access control
- Permission management
- User registration and authentication

### Reporting
- Monthly sales charts
- Sales comparison over time
- Top products analysis
- Top categories analysis
- Activity logging and audit trails
- Data export (Excel, CSV, PDF)

## Code Quality

### Strengths

1. **Modern Laravel Practices**:
   - Uses Laravel 10.x features
   - Leverages Eloquent relationships
   - Implements middleware for authorization
   - Uses resource controllers

2. **Well-Structured Views**:
   - Separated admin and public views
   - Organized with layouts, partials, and pages

3. **Authorization System**:
   - Implements Spatie Laravel Permission
   - Role-based access control

4. **Activity Logging**:
   - Tracks changes to important models
   - Provides accountability and audit trail

### Areas for Improvement

1. **Inconsistent Naming**:
   - Some models use singular English names (News, Member)
   - Others use Indonesian names (Galeri, Pesan)

2. **Controller Size**:
   - Some controllers (e.g., HomeController) have many methods and are quite large
   - Could benefit from extraction of service classes

3. **Legacy References**:
   - References to original organization should be removed/refactored

4. **Missing Tests**:
   - Limited test coverage observed

## Recommendations

1. **Standardize Naming Conventions**:
   - Choose either English or Indonesian consistently for model and table names
   - Follow Laravel naming conventions consistently

2. **Refactor Large Controllers**:
   - Extract service classes for business logic
   - Implement the repository pattern for database operations

3. **Enhance Security**:
   - Review file upload handling for potential vulnerabilities
   - Implement API rate limiting
   - Ensure proper validation on all inputs

4. **Add Testing**:
   - Implement unit tests for models and services
   - Add feature tests for critical user flows
   - Set up CI/CD pipeline for automated testing

5. **Performance Optimization**:
   - Implement caching for frequently accessed data
   - Optimize database queries with indexes
   - Consider eager loading relationships to prevent N+1 query issues

6. **Documentation**:
   - Add PHPDoc comments to classes and methods
   - Create developer documentation for API endpoints

## Setup Instructions

1. **Clone the Repository**:
   ```bash
   git clone <repository-url>
   cd nebulae-cymbals
   ```

2. **Install Dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Environment Configuration**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Setup**:
   ```bash
   # Configure database credentials in .env
   php artisan migrate
   php artisan db:seed  # If seeders are available
   ```

5. **Storage Links**:
   ```bash
   php artisan storage:link
   ```

6. **Build Assets**:
   ```bash
   npm run dev  # For development
   # or
   npm run build  # For production
   ```

7. **Run the Application**:
   ```bash
   php artisan serve
   ```

## Contribution Guidelines

1. **Coding Standards**:
   - Follow PSR-12 coding standards
   - Use 4 spaces for indentation (no tabs)
   - Use camelCase for variables and methods
   - Use PascalCase for class names

2. **Git Workflow**:
   - Create feature branches from the main branch
   - Use descriptive branch names (feature/add-calendar, fix/member-validation)
   - Write clear commit messages

3. **Pull Requests**:
   - Provide a clear description of changes
   - Reference related issues
   - Ensure all tests pass
   - Request code reviews from team members

4. **Documentation**:
   - Update README.md when adding new features
   - Document complex logic with comments
   - Add PHPDoc comments to public methods

5. **Testing**:
   - Write tests for new features
   - Ensure existing tests pass before submitting PR
