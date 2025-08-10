<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Priority System Test</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/offer-priority-badges.css') }}" rel="stylesheet">
    <style>
        .test-card {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .test-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .priority-badge {
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .priority-offers-page {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            animation: pulse-glow 2s infinite;
        }
        
        .priority-product-onboarding {
            background: linear-gradient(45deg, #17a2b8, #6f42c1);
            color: white;
        }
        
        .priority-none {
            background: #6c757d;
            color: white;
        }
        
        .price-comparison {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
        }
        
        .savings-highlight {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin: 5px 0;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="text-center mb-5">
                    <h1 class="display-4 text-primary">
                        <i class="fas fa-layer-group"></i>
                        Offer Priority System Test
                    </h1>
                    <p class="lead text-muted">Testing the implementation of offer priority: Offers Page → Product Onboarding</p>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="alert alert-success">
                    <h5><i class="fas fa-check-circle"></i> Priority Rules</h5>
                    <ol class="mb-0">
                        <li><strong>First Priority:</strong> Offers from "Offers Page/Menu" (highest discount)</li>
                        <li><strong>Second Priority:</strong> Product onboarding discount</li>
                        <li><strong>Fallback:</strong> No discount (original price)</li>
                    </ol>
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle"></i> Implementation Status</h5>
                    <ul class="mb-0">
                        <li>✅ Product Model updated with priority logic</li>
                        <li>✅ Frontend components show offer source</li>
                        <li>✅ Visual indicators for different offer types</li>
                        <li>✅ Backward compatibility maintained</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            @forelse($testResults as $result)
                <div class="col-lg-6 mb-4">
                    <div class="test-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-box text-primary"></i>
                                {{ $result['product_name'] }}
                            </h5>
                            
                            @if($result['active_offer_source'])
                                @if($result['active_offer_source'] === 'offers_page')
                                    <span class="priority-badge priority-offers-page">
                                        <i class="fas fa-fire"></i> Special Offer
                                    </span>
                                @elseif($result['active_offer_source'] === 'product_onboarding')
                                    <span class="priority-badge priority-product-onboarding">
                                        <i class="fas fa-tag"></i> Product Discount
                                    </span>
                                @endif
                            @else
                                <span class="priority-badge priority-none">
                                    No Offers
                                </span>
                            @endif
                        </div>

                        <div class="price-comparison">
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted d-block">Original Price</small>
                                    <h4 class="text-muted">₹{{ number_format($result['original_price'], 2) }}</h4>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Final Price</small>
                                    <h4 class="text-success">₹{{ number_format($result['final_price'], 2) }}</h4>
                                </div>
                            </div>
                            
                            @if($result['savings'] > 0)
                                <div class="text-center mt-3">
                                    <span class="savings-highlight">
                                        <i class="fas fa-piggy-bank"></i>
                                        You Save ₹{{ number_format($result['savings'], 2) }}
                                        ({{ round($result['discount_percentage'], 1) }}% OFF)
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="mt-3">
                            <h6 class="text-dark">Available Discounts:</h6>
                            <ul class="list-unstyled">
                                @if($result['has_offers_page_offers'])
                                    <li class="text-success">
                                        <i class="fas fa-fire"></i>
                                        <strong>Offers Page:</strong> Active offer available
                                        @if($result['active_offer_source'] === 'offers_page')
                                            <span class="badge bg-success ms-2">ACTIVE</span>
                                        @endif
                                    </li>
                                @else
                                    <li class="text-muted">
                                        <i class="fas fa-times"></i>
                                        <strong>Offers Page:</strong> No active offers
                                    </li>
                                @endif
                                
                                @if($result['has_product_onboarding_discount'])
                                    <li class="text-info">
                                        <i class="fas fa-tag"></i>
                                        <strong>Product Discount:</strong> ₹{{ number_format($result['product_onboarding_discount'], 2) }}
                                        @if($result['active_offer_source'] === 'product_onboarding')
                                            <span class="badge bg-info ms-2">ACTIVE</span>
                                        @elseif($result['has_offers_page_offers'])
                                            <span class="badge bg-warning text-dark ms-2">OVERRIDDEN</span>
                                        @endif
                                    </li>
                                @else
                                    <li class="text-muted">
                                        <i class="fas fa-times"></i>
                                        <strong>Product Discount:</strong> Not set
                                    </li>
                                @endif
                            </ul>
                        </div>

                        @if($result['offer_details'])
                            <div class="mt-3 p-3 bg-light rounded">
                                <h6 class="text-primary mb-2">
                                    <i class="fas fa-info-circle"></i>
                                    Active Offer Details
                                </h6>
                                <small class="text-muted d-block">
                                    <strong>Source:</strong> 
                                    @if($result['offer_details']['source'] === 'offers_page')
                                        🎯 Special Offers Menu
                                    @elseif($result['offer_details']['source'] === 'product_onboarding')
                                        💰 Product Setup Discount
                                    @endif
                                </small>
                                <small class="text-muted d-block">
                                    <strong>Offer Name:</strong> {{ $result['offer_details']['offer_name'] }}
                                </small>
                                <small class="text-muted d-block">
                                    <strong>Discount:</strong> {{ round($result['offer_details']['discount_percentage'], 1) }}% 
                                    (₹{{ number_format($result['offer_details']['discount_amount'], 2) }})
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        <h5><i class="fas fa-exclamation-triangle"></i> No Test Data Available</h5>
                        <p>No products found for testing. Please create some products first.</p>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Test Product
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <div class="alert alert-success">
                    <h4 class="alert-heading">
                        <i class="fas fa-check-circle"></i>
                        Implementation Complete!
                    </h4>
                    <p>The offer priority system has been successfully implemented with the following features:</p>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Backend Features:</h6>
                            <ul>
                                <li>Priority-based offer selection</li>
                                <li>Virtual offer objects for onboarding discounts</li>
                                <li>Comprehensive offer details API</li>
                                <li>Backward compatibility</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Frontend Features:</h6>
                            <ul>
                                <li>Visual indicators for offer sources</li>
                                <li>Priority badges and animations</li>
                                <li>Detailed offer information display</li>
                                <li>Responsive design</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="/admin/products" class="btn btn-primary btn-lg">
                <i class="fas fa-arrow-left"></i> Back to Products
            </a>
            <a href="/admin/offers" class="btn btn-success btn-lg ms-3">
                <i class="fas fa-fire"></i> Manage Offers
            </a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
