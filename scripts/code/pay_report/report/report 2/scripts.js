let filterData = {};

let first = true;

document.addEventListener('DOMContentLoaded', function() {


    const tabs = document.querySelectorAll('.nav-link'); 
    tabs.forEach(tab => {
        tab.addEventListener('click', function(event) {
            event.preventDefault(); 

            if(first === true) document.getElementById('filtersForm').dispatchEvent(new Event('submit'));

            // Deactivate all tabs
            tabs.forEach(innerTab => {
                innerTab.classList.remove('active');
            });

            // Deactivate all tab contents
            const tabContents = document.querySelectorAll('.tab-pane');
            tabContents.forEach(content => {
                content.classList.remove('show');
                content.classList.remove('active');
            });

            // Activate the clicked tab
            event.target.classList.add('active');

            // Special handling for UTM tab
            if (event.target.id === 'byUtm-tab') {
                // Activate all UTM report contents
                document.getElementById('reportByUtmTable').classList.add('show');
                document.getElementById('reportByUtmTable').classList.add('active');
                const utmContents = document.querySelectorAll('.utm-report-content');
                utmContents.forEach(content => {
                    content.classList.add('show');
                    content.classList.add('active');
                });
            } else {
                // Activate the corresponding tab content
                const targetContent = document.getElementById(event.target.getAttribute('href').substr(1)); 
                targetContent.classList.add('show');
                targetContent.classList.add('active');
            }
        });
    });


    fetch('/server.php')
    .then(response => response.json()) 
    .then(data => {
        filterData = data;

        document.querySelector("input[name='startDate']").value = data.earliest_date;
        document.querySelector("input[name='endDate']").value = data.current_date;

        // console.log(data);
        
        const prependEmptyOption = (selectElement) => {
            const emptyOption = document.createElement('option');
            emptyOption.value = '';
            emptyOption.textContent = '-- Выберите --'; 
            // console.log(selectElement)
            selectElement.prepend(emptyOption);
        };


        const razdelSelect = document.querySelector('[name="razdel"]');
        prependEmptyOption(razdelSelect);
        data.razdel.forEach(razdel => {
            const option = document.createElement('option');
            option.value = razdel.id;
            option.textContent = razdel.name;
            razdelSelect.appendChild(option);
        });

        const tagsSelect = document.querySelector('[name="tags"]');
        prependEmptyOption(tagsSelect);
        data.tags.forEach(tag => {
            const option = document.createElement('option');
            option.value = tag.id;
            option.textContent = tag.name;
            tagsSelect.appendChild(option);
        });

        const managerSelect = document.querySelector('[name="manager"]');
        prependEmptyOption(managerSelect);
        data.manager.forEach(manager => {
            const option = document.createElement('option');
            option.value = manager.id; 
            option.textContent = manager.name; 
            managerSelect.appendChild(option);
        });

        const partnerSelect = document.querySelector('[name="partner"]');
        prependEmptyOption(partnerSelect);
        data.partner.forEach(partner => {
            const option = document.createElement('option');
            option.value = partner.id; 
            option.textContent = partner.name; 
            partnerSelect.appendChild(option);
        });

        const productSelect = document.querySelector('[name="product"]');
        prependEmptyOption(productSelect);
        data.product.forEach(product => {
            const option = document.createElement('option');
            option.value = product.id; 
            option.textContent = product.description; 
            productSelect.appendChild(option);
        });

        const utmSourceSelect = document.querySelector('[name="utm_source"]');
        prependEmptyOption(utmSourceSelect);
        data.utm.utm_source.forEach(source => {
            const option = document.createElement('option');
            option.value = source;
            option.textContent = source;
            utmSourceSelect.appendChild(option);
        });

        const utmMediumSelect = document.querySelector('[name="utm_medium"]');
        prependEmptyOption(utmMediumSelect);
        data.utm.utm_medium.forEach(medium => {
            const option = document.createElement('option');
            option.value = medium;
            option.textContent = medium;
            utmMediumSelect.appendChild(option);
        });

        const utmCampaignSelect = document.querySelector('[name="utm_campaign"]');
        prependEmptyOption(utmCampaignSelect);
        data.utm.utm_campaign.forEach(campaign => {
            const option = document.createElement('option');
            option.value = campaign;
            option.textContent = campaign;
            utmCampaignSelect.appendChild(option);
        });

        const utmContentSelect = document.querySelector('[name="utm_content"]');
        prependEmptyOption(utmContentSelect);
        data.utm.utm_content.forEach(content => {
            const option = document.createElement('option');
            option.value = content;
            option.textContent = content;
            utmContentSelect.appendChild(option);
        });

        const utmTermSelect = document.querySelector('[name="utm_term"]');
        prependEmptyOption(utmTermSelect);
        data.utm.utm_term.forEach(term => {
            const option = document.createElement('option');
            option.value = term;
            option.textContent = term;
            utmTermSelect.appendChild(option);
        });


    }).catch(error => {
        console.error('There was a problem with the fetch operation:', error);
    });
});


document.getElementById('filtersForm').addEventListener('submit', function(e) {
    e.preventDefault();
    first = false;

    clearAllTables();


    

    const formData = new FormData(this);

    // formData.forEach((value, key) => {
    //     console.log(key + ' = ' + value);
    // });
    const selectedReports = {
        utm_source: formData.get('utm_source'),
        utm_medium: formData.get('utm_medium'),
        utm_campaign: formData.get('utm_campaign'),
        utm_content: formData.get('utm_content'),
        utm_term: formData.get('utm_term'),
        razdel: formData.get('razdel') 
            ? (filterData.razdel.find(razdel => razdel.id === formData.get('razdel')) || {}).name 
            : null,
        tags: formData.get('tags') 
            ? (filterData.tags.find(tag => tag.id === formData.get('tags')) || {}).name 
            : null,
        manager: formData.get('manager') 
            ? (filterData.manager.find(manager => manager.id === formData.get('manager')) || {}).name 
            : null,
        partner: formData.get('partner') 
            ? (filterData.partner.find(partner => partner.id === formData.get('partner')) || {}).name 
            : null,
        product: formData.get('product') 
            ? (filterData.product.find(product => product.id === formData.get('product')) || {}).description 
            : null
    };

    // Fetch report data from server
    fetch('/server.php', {
        method: 'POST',
        body: formData
    })
    // .then(response => response.json())
    .then(response => response.text())
    .then(bodyText => {console.log(bodyText); return JSON.parse(bodyText)})
    .then(data => {
        const byDay = {};
        const byWeek = {};
        const byMonth = {};
        const byQuarter = {};
        const byRazdel = {};
        const byTags = {};
        const byManagers = {};
        const byPartners = {};
        const byProducts = {};

        const byUtmSource = {};
        const byUtmMedium = {};
        const byUtmCampaign = {};
        const byUtmContent = {};
        const byUtmTerm = {};
    
        data.forEach(item => {
            const itemAmount = parseFloat(item.amount);
            const date = new Date(item.paymentDate);
            const year = date.getFullYear();
            const month = date.getMonth() + 1; // Month is 0-indexed
            const day = date.getDate();
            const week = getWeekNumber(date);
    
            function groupData(data, key, amount) {
                data[key] = data[key] || {count: 0, amount: 0};
                data[key].amount += amount;
                data[key].count++;
            }
    
            const dayKey = `${day}.${month}.${year}`;
            groupData(byDay, dayKey, itemAmount);
    
            const weekKey = `W${week}.${year}`;
            groupData(byWeek, weekKey, itemAmount);
    
            const monthKey = `${month}.${year}`;
            groupData(byMonth, monthKey, itemAmount);
    
            const quarterKey = `Q${Math.ceil(month / 3)}.${year}`;
            groupData(byQuarter, quarterKey, itemAmount);
    
            if (item.razdel_name) groupData(byRazdel, item.razdel_name, itemAmount);
            if (item.tag_name) groupData(byTags, item.tag_name, itemAmount);
            if (item.manager) groupData(byManagers, item.manager, itemAmount);
            if (item.partner) groupData(byPartners, item.partner, itemAmount);
            if (item.product_description) groupData(byProducts, item.product_description, itemAmount);
            if (item.utm_source) groupData(byUtmSource, item.utm_source, itemAmount);
            if (item.utm_medium) groupData(byUtmMedium, item.utm_medium, itemAmount);
            if (item.utm_campaign) groupData(byUtmCampaign, item.utm_campaign, itemAmount);
            if (item.utm_content) groupData(byUtmContent, item.utm_content, itemAmount);
            if (item.utm_term) groupData(byUtmTerm, item.utm_term, itemAmount);
        });
    
        populateTable('reportByDayTable', byDay);
        populateTable('reportByWeekTable', byWeek);
        populateTable('reportByMonthTable', byMonth);
        populateTable('reportByQuarterTable', byQuarter);
        populateTable('reportByRazdelTable', byRazdel);
        populateTable('reportByTagTable', byTags);
        populateTable('reportByManagerTable', byManagers);
        populateTable('reportByPartnerTable', byPartners);
        populateTable('reportByProductTable', byProducts);
        populateTable('reportByUtmSourceTable', byUtmSource);
        populateTable('reportByUtmMediumTable', byUtmMedium);
        populateTable('reportByUtmCampaignTable', byUtmCampaign);
        populateTable('reportByUtmContentTable', byUtmContent);
        populateTable('reportByUtmTermTable', byUtmTerm);
    });
    
    function getWeekNumber(d) {
        d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
        
        d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay() || 7));
        const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
        const weekNo = Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
        return weekNo;
    }
    
    function populateTable(tableId, data) {
        const tableBody = document.querySelector(`#${tableId} tbody`);
    
        const totalAmount = Object.values(data).reduce((acc, item) => acc + item.amount, 0);
        
        let totalCount = 0;
        let totalSum = 0;
    
        tableBody.innerHTML = '';
    
        for (const [key, value] of Object.entries(data)) {
            const row = document.createElement('tr');
            
            const periodCell = document.createElement('td');
            periodCell.textContent = key;
            row.appendChild(periodCell);
    
            const countCell = document.createElement('td');
            countCell.textContent = value.count;
            row.appendChild(countCell);
            totalCount += value.count;
    
            const sumCell = document.createElement('td');
            sumCell.textContent = value.amount;
            row.appendChild(sumCell);
            totalSum += value.amount;
    
            const percentageCell = document.createElement('td');
            percentageCell.textContent = ((value.amount / totalAmount) * 100).toFixed(2) + '%';
            row.appendChild(percentageCell);
    
            tableBody.appendChild(row);
        }
    
        document.querySelector(`#${tableId}-total-count`).textContent = totalCount;
        document.querySelector(`#${tableId}-total-sum`).textContent = totalSum.toFixed(2);
    }

    function clearAllTables() {
        const tables = document.querySelectorAll("#reportTabContent table");
        tables.forEach(table => {
            const tbody = table.querySelector("tbody");
            if (tbody) {
                tbody.innerHTML = '';
            }
    
            // Clear the totals in the tfoot section
            const tfoot = table.querySelector("tfoot");
            if (tfoot) {
                const totalCountCell = tfoot.querySelector("[id$='-total-count']");
                const totalSumCell = tfoot.querySelector("[id$='-total-sum']");
                
                if (totalCountCell) {
                    totalCountCell.textContent = '';
                }
    
                if (totalSumCell) {
                    totalSumCell.textContent = '';
                }
            }
        });
    }
});




function clearFilters() {
    // Reset date inputs to default values
    document.querySelector("input[name='startDate']").value = filterData.earliest_date;
    document.querySelector("input[name='endDate']").value = filterData.current_date;
    
    // Clear select dropdowns
    const selectElements = [
        '[name="utm_source"]', '[name="utm_medium"]', '[name="utm_campaign"]',
        '[name="utm_content"]', '[name="utm_term"]', '[name="razdel"]',
        '[name="tags"]', '[name="manager"]', '[name="partner"]', '[name="product"]'
    ];
    
    selectElements.forEach(selector => {
        const select = document.querySelector(selector);
        if (select) {
            select.selectedIndex = 0;
        }
    });
}


document.getElementById('dropFiltersBtn').addEventListener('click', function() {
    clearFilters();
});


