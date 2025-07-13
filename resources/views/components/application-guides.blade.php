@props(['product' => null])

@if($product)
<div class="application-guides">
    <h3>Application Guide</h3>
    <div class="guide-content">
        <h4>Primary Applications</h4>
        <ul>
            <li>Laboratory research and analysis</li>
            <li>Quality control and testing</li>
            <li>Educational and training purposes</li>
            <li>Industrial process monitoring</li>
        </ul>
        
        <h4>Best Practices</h4>
        <ul>
            <li>Follow manufacturer guidelines</li>
            <li>Regular calibration and maintenance</li>
            <li>Proper safety protocols</li>
            <li>Quality assurance procedures</li>
        </ul>
    </div>
</div>
@endif