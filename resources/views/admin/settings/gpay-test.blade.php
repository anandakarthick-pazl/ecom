@extends('admin.layouts.app')

@section('title', 'G Pay Test')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">G Pay Number Test</h6>
                    </div>
                    <div class="card-body">
                        <div id="test-results">
                            <p>Loading test results...</p>
                        </div>
                        
                        <hr>
                        
                        <h5>Quick Test Form</h5>
                        <form id="quick-test-form">
                            @csrf
                            <div class="form-group">
                                <label for="test_gpay">Test G Pay Number:</label>
                                <input type="text" class="form-control" id="test_gpay" name="gpay_number" 
                                       placeholder="+91 9876543210" value="{{ $company->gpay_number ?? '' }}">
                            </div>
                            <button type="button" class="btn btn-primary" onclick="testGPaySave()">
                                Test Save
                            </button>
                            <button type="button" class="btn btn-info" onclick="loadTestResults()">
                                Refresh Test
                            </button>
                        </form>
                        
                        <div id="test-output" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load test results on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadTestResults();
        });
        
        function loadTestResults() {
            fetch('/admin/settings/gpay-test-data')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('test-results').innerHTML = `
                        <h5>Current Company Data:</h5>
                        <table class="table table-bordered">
                            <tr><td><strong>Company Name:</strong></td><td>${data.company_name || 'N/A'}</td></tr>
                            <tr><td><strong>G Pay Number:</strong></td><td>${data.gpay_number || 'NULL'}</td></tr>
                            <tr><td><strong>WhatsApp:</strong></td><td>${data.whatsapp_number || 'NULL'}</td></tr>
                            <tr><td><strong>Mobile:</strong></td><td>${data.mobile_number || 'NULL'}</td></tr>
                            <tr><td><strong>Alternate:</strong></td><td>${data.alternate_phone || 'NULL'}</td></tr>
                        </table>
                        
                        <h5>Database Info:</h5>
                        <p><strong>Fillable Fields:</strong> ${data.fillable_fields.join(', ')}</p>
                        <p><strong>G Pay in Fillable:</strong> ${data.fillable_fields.includes('gpay_number') ? 'YES' : 'NO'}</p>
                    `;
                })
                .catch(error => {
                    document.getElementById('test-results').innerHTML = `
                        <div class="alert alert-danger">Error loading test data: ${error.message}</div>
                    `;
                });
        }
        
        function testGPaySave() {
            const gpayValue = document.getElementById('test_gpay').value;
            const formData = new FormData();
            formData.append('_token', document.querySelector('input[name="_token"]').value);
            formData.append('gpay_number', gpayValue);
            formData.append('test_mode', '1');
            
            fetch('/admin/settings/gpay-test-save', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('test-output').innerHTML = `
                    <div class="alert ${data.success ? 'alert-success' : 'alert-danger'}">
                        <strong>Test Result:</strong> ${data.message}<br>
                        <strong>Saved Value:</strong> ${data.saved_value || 'NULL'}<br>
                        <strong>Database Value:</strong> ${data.db_value || 'NULL'}
                    </div>
                `;
                
                // Refresh the test results
                setTimeout(loadTestResults, 1000);
            })
            .catch(error => {
                document.getElementById('test-output').innerHTML = `
                    <div class="alert alert-danger">Error: ${error.message}</div>
                `;
            });
        }
    </script>
@endsection
