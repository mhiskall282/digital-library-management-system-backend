<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Business Information Systems (BIS)
            [
                'name' => 'Introduction to Business Information Systems',
                'course_code' => 'BIS 111',
                'course_name' => 'Introduction to Business Information Systems',
                'program' => 'BSc. Business Information Systems (BIS)',
                'level' => 'L100',
                'semester' => 'FIRST',
                'description' => 'Information architecture, hardware/software infrastructure, data management, and digital enterprise fundamentals.',
            ],
            [
                'name' => 'Database Design & SQL for Business',
                'course_code' => 'BIS 211',
                'course_name' => 'Database Design & SQL for Business',
                'program' => 'BSc. Business Information Systems (BIS)',
                'level' => 'L200',
                'semester' => 'FIRST',
                'description' => 'Relational database schema modeling, ER diagrams, normalization, and SQL data queries.',
            ],
            [
                'name' => 'Systems Analysis & Enterprise Design',
                'course_code' => 'BIS 311',
                'course_name' => 'Systems Analysis & Enterprise Design',
                'program' => 'BSc. Business Information Systems (BIS)',
                'level' => 'L300',
                'semester' => 'FIRST',
                'description' => 'Agile systems development life cycle (SDLC), UML design, feasibility studies, and user interface workflows.',
            ],
            [
                'name' => 'Enterprise Resource Planning & E-Commerce',
                'course_code' => 'BIS 411',
                'course_name' => 'Enterprise Resource Planning & E-Commerce',
                'program' => 'BSc. Business Information Systems (BIS)',
                'level' => 'L400',
                'semester' => 'FIRST',
                'description' => 'ERP implementations (SAP/Odoo), digital payment gateways, cloud commerce architecture, and cybersecurity.',
            ],

            // Banking and Finance
            [
                'name' => 'Banking Operations and Practice',
                'course_code' => 'BNF 211',
                'course_name' => 'Banking Operations and Practice',
                'program' => 'BSc. Banking and Finance',
                'level' => 'L200',
                'semester' => 'FIRST',
                'description' => 'Central banking principles, credit underwriting, clearing systems, and Ghanaian prudential banking regulations.',
            ],
            [
                'name' => 'Corporate Finance',
                'course_code' => 'FIN 311',
                'course_name' => 'Corporate Finance',
                'program' => 'BSc. Banking and Finance',
                'level' => 'L300',
                'semester' => 'FIRST',
                'description' => 'Capital budgeting, cost of capital, dividend policy, capital structure, and risk-return trade-offs.',
            ],
            [
                'name' => 'Financial Markets and Portfolio Analysis',
                'course_code' => 'FIN 411',
                'course_name' => 'Financial Markets and Portfolio Analysis',
                'program' => 'BSc. Banking and Finance',
                'level' => 'L400',
                'semester' => 'FIRST',
                'description' => 'Modern portfolio theory, capital asset pricing model (CAPM), bond valuation, and Ghana Stock Exchange operations.',
            ],

            // Accounting
            [
                'name' => 'Financial Accounting I',
                'course_code' => 'ACT 211',
                'course_name' => 'Financial Accounting I',
                'program' => 'BSc. Accounting',
                'level' => 'L200',
                'semester' => 'FIRST',
                'description' => 'Double-entry bookkeeping, trial balance, preparation of financial statements under IFRS.',
            ],
            [
                'name' => 'Cost and Management Accounting',
                'course_code' => 'ACT 321',
                'course_name' => 'Cost and Management Accounting',
                'program' => 'BSc. Accounting',
                'level' => 'L300',
                'semester' => 'SECOND',
                'description' => 'Cost classification, job/process costing, variance analysis, and budgetary control techniques.',
            ],
            [
                'name' => 'Auditing and Assurance Services',
                'course_code' => 'ACT 411',
                'course_name' => 'Auditing and Assurance Services',
                'program' => 'BSc. Accounting',
                'level' => 'L400',
                'semester' => 'FIRST',
                'description' => 'Audit risk modeling, internal controls evaluation, statutory reporting, and professional code of ethics.',
            ],

            // Marketing
            [
                'name' => 'Principles of Marketing',
                'course_code' => 'MKT 211',
                'course_name' => 'Principles of Marketing',
                'program' => 'BBA. Marketing',
                'level' => 'L200',
                'semester' => 'FIRST',
                'description' => 'The 4Ps, consumer buyer behavior, market segmentation, targeting, and competitive analysis.',
            ],
            [
                'name' => 'Digital Marketing & Social Commerce',
                'course_code' => 'MKT 312',
                'course_name' => 'Digital Marketing & Social Commerce',
                'program' => 'BBA. Marketing',
                'level' => 'L300',
                'semester' => 'FIRST',
                'description' => 'Search engine marketing, social media acquisition funnels, content strategies, and web analytics.',
            ],
            [
                'name' => 'Integrated Marketing Communications',
                'course_code' => 'MKT 411',
                'course_name' => 'Integrated Marketing Communications',
                'program' => 'BBA. Marketing',
                'level' => 'L400',
                'semester' => 'FIRST',
                'description' => 'Advertising management, public relations, promotional campaign design, and media planning.',
            ],

            // Human Resource Management
            [
                'name' => 'Human Resource Management',
                'course_code' => 'HRM 312',
                'course_name' => 'Human Resource Management',
                'program' => 'BBA. Human Resource Management',
                'level' => 'L300',
                'semester' => 'FIRST',
                'description' => 'Talent acquisition, performance appraisal, compensation systems, and labor relations.',
            ],
            [
                'name' => 'Industrial Relations & Labor Law',
                'course_code' => 'HRM 411',
                'course_name' => 'Industrial Relations & Labor Law',
                'program' => 'BBA. Human Resource Management',
                'level' => 'L400',
                'semester' => 'FIRST',
                'description' => 'Collective bargaining, trade union dynamics, dispute resolution, and the Ghana Labour Act 2003.',
            ],

            // General Business / Common Core
            [
                'name' => 'Principles of Management',
                'course_code' => 'BBA 111',
                'course_name' => 'Principles of Management',
                'program' => 'General Business',
                'level' => 'L100',
                'semester' => 'FIRST',
                'description' => 'Fundamental concepts of organizational leadership, planning, and control in business operations.',
            ],
            [
                'name' => 'Business Mathematics',
                'course_code' => 'BBA 121',
                'course_name' => 'Business Mathematics',
                'program' => 'General Business',
                'level' => 'L100',
                'semester' => 'SECOND',
                'description' => 'Quantitative methods, interest theory, calculus for optimization, and linear algebra applications in commerce.',
            ],
            [
                'name' => 'Strategic Management',
                'course_code' => 'BBA 411',
                'course_name' => 'Strategic Management',
                'program' => 'General Business',
                'level' => 'L400',
                'semester' => 'FIRST',
                'description' => 'Industry analysis, SWOT, Porter generic strategies, corporate diversification, and strategy execution.',
            ],

            // Postgraduate
            [
                'name' => 'Advanced Strategic Marketing',
                'course_code' => 'MBA 811',
                'course_name' => 'Advanced Strategic Marketing',
                'program' => 'MBA. Business Administration',
                'level' => 'MASTERS',
                'semester' => 'FIRST',
                'description' => 'Executive level market entry strategies, brand valuation, and global supply chain positioning.',
            ],
            [
                'name' => 'Advanced Quantitative Research Methods',
                'course_code' => 'PHD 901',
                'course_name' => 'Advanced Quantitative Research Methods',
                'program' => 'PhD. Business Administration',
                'level' => 'PHD',
                'semester' => 'FIRST',
                'description' => 'Econometric modeling, structural equation modeling (SEM), and multivariate statistical techniques.',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['course_code' => $cat['course_code'], 'level' => $cat['level'], 'semester' => $cat['semester']],
                $cat
            );
        }
    }
}
