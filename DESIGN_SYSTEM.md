# Fittable - Design System

Job-Skills Matching Platform - $0.25 per Analysis

## 🎨 Design Theme - Light Theme

### Color Palette

#### Background Colors
- **Main BG**: `#ffffff` (White - primary background)
- **Secondary BG**: `#f8fafc` (Very light gray - sections)
- **Card BG**: `#ffffff` (White cards with borders)
- **Subtle BG**: `#f1f5f9` (Light gray for input fields)

#### Accent Colors (Magenta/Dark)
- **Primary Magenta**: `#e900ff` / `#ff00ff` (CTAs, highlights, borders)
- **Dark Navy**: `#1a1d2e` (Text, important elements)
- **Deep Purple**: `#8b5cf6` (Secondary accents, recruiter mode)
- **Dark Gray**: `#0f111a` (Headings, strong emphasis)

#### Text Colors
- **Primary Text**: `#1a1d2e` (Dark navy)
- **Secondary Text**: `#64748b` (Medium gray)
- **Muted Text**: `#94a3b8` (Light gray)
- **Link Color**: `#e900ff` (Magenta)

#### Status Colors
- **Success Green**: `#10b981` (Good match indicators)
- **Warning Orange**: `#f59e0b` (Moderate fit)
- **Alert Red**: `#ef4444` (Gaps/missing requirements)
- **Info Blue**: `#3b82f6` (Informational)

#### Borders & Accents
- **Magenta Border**: `#e900ff` (2px for emphasis)
- **Light Border**: `#e2e8f0` (1px for cards)
- **Input Border**: `#cbd5e1`
- **Focus Border**: `#e900ff`

### Typography

- **All Text**: 'Courier New', Courier, monospace
- **Headings**: Bold monospace
- **Body**: Regular monospace
- **No custom fonts** - System monospace throughout

### Design Style

- **No Rounded Corners**: All elements use sharp, square corners (border-radius: 0)
- **Light & Clean**: White/light gray backgrounds
- **Magenta Accents**: Bold magenta (#e900ff) for CTAs, borders, and emphasis
- **Dark Sections**: Very dark navy (#1e293b) for hero/header sections
- **High Contrast**: Dark text on light backgrounds
- **Card-based Layout**: White cards with magenta or gray borders
- **No Shadows**: Flat design with borders instead of shadows
- **Sharp Focus**: 2px magenta borders for interactive elements

### Tailwind CSS Custom Classes

```
bg-main → #ffffff
bg-secondary → #f8fafc
bg-card → #ffffff
bg-subtle → #f1f5f9

text-primary → #1a1d2e
text-secondary → #64748b
text-muted → #94a3b8

border-magenta → #e900ff
border-light → #e2e8f0

accent-magenta → #e900ff
accent-navy → #1a1d2e
accent-purple → #8b5cf6
```

### Component Patterns

#### Cards
- Background: White (#ffffff)
- Border: 2px solid #e2e8f0 or 2px solid #e900ff (for emphasis)
- **No rounded corners** (border-radius: 0)
- **No shadows** - use borders instead

#### Buttons (Primary)
- Background: #e900ff (Magenta)
- Text: White
- Border: None or 2px solid #e900ff
- **No rounded corners**
- Hover: Slightly darker magenta

#### Input Fields
- Background: #f1f5f9 (Subtle gray) or transparent with border
- Border: 2px solid #cbd5e1
- Focus: 2px solid #e900ff (Magenta)
- Text: #1a1d2e
- **No rounded corners**

#### Status Indicators
- Good Match: #10b981 (Green)
- Moderate Fit: #f59e0b (Orange)
- Poor Match: #ef4444 (Red)
- Info: #3b82f6 (Blue)

#### Hero/Header Sections
- Background: #1e293b (Dark navy)
- Text: White (#ffffff)
- **No rounded corners**
