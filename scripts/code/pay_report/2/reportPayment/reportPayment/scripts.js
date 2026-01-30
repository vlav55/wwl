let filterData = {}, currentFilters = {};

const filterMapping = {
    tags: "tag_id",
    razdel: "razdel_id",
    manager: "manager_id",
    partner: "partner_id",
    product: "product_id",
    utm_source: "utm_source",
    utm_medium: "utm_medium",
    utm_campaign: "utm_campaign",
    utm_content: "utm_content",
    utm_term: "utm_term"
};

let first = true;

document.addEventListener('DOMContentLoaded', function() {

    const modal = document.querySelector('#detailsModal');
    const closeModalBtn = document.querySelector(".close-btn");
    // console.log(modal);

    closeModalBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    // Optional: Close the modal if user clicks outside of it
    window.onclick = (event) => {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }


    const tabs = document.querySelectorAll('.reportp-nav'); 
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
    // .then(response => response.text())
    // .then(bodyText => {console.log(bodyText); return JSON.parse(bodyText)})
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

const formDataToJSON = formData => {
    const obj = {};
    formData.forEach((value, key) => {
        if (obj[key]) {
            if (Array.isArray(obj[key])) {
                obj[key].push(value);
            } else {
                obj[key] = [obj[key], value];
            }
        } else {
            obj[key] = value;
        }
    });
    return obj;
};


document.getElementById('filtersForm').addEventListener('submit', function(e) {
    e.preventDefault();
    first = false;

    clearAllTables();


    

    const formData = new FormData(this);

    const rawFilters = formDataToJSON(formData);
    const mappedFilters = {}
    for (let key in rawFilters) {
        if (filterMapping[key]) {
            mappedFilters[filterMapping[key]] = rawFilters[key];
        }
    }
    currentFilters = [mappedFilters]

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

    document.getElementById("dataLoader").style.display = "block";
    // Fetch report data from server
    fetch('/server.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    // .then(response => response.text())
    // .then(bodyText => {console.log(bodyText); return JSON.parse(bodyText)})
    .then(data => {

        const groupings = [
            {
                name: 'Day', 
                
            },
            {
                name: 'Week', 
               
            },
            {
                name: 'Month', 
               
            },
            {
                name: 'Quarter', 
               
            },
            { name: 'Razdel'},
            { name: 'Tag'},
            { name: 'Manager'},
            { name: 'Product'},
            { name: 'Partner'},
            { name: 'UtmSource'},
            { name: 'UtmMedium'},
            { name: 'UtmCampaign'},
            { name: 'UtmContent'},
            { name: 'UtmTerm'}
        ];

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
            const itemAmount = parseInt(item.amount);
            const date = new Date(item.paymentDate);
            const year = date.getFullYear();
            const month = date.getMonth() + 1; // Month is 0-indexed
            const day = date.getDate();
            const week = getWeekNumber(date);
    
            function groupData(data, filterKey, filter, key, amount, order_number) {
                data[key] = data[key] || {count: 0, amount: 0, filters: [], order_numbers: []};
                
                
                if (data[key].order_numbers.includes(order_number)) {
                    return; 
                }

                
            
                data[key].amount += amount;
                data[key].count++;
            
                const filterObject = {[filterKey]: filter};
            
                if (!data[key].filters.some(obj => obj[filterKey] === filter)) {
                    data[key].filters.push(filterObject);
                }
            
            
                // Add the order_number to the order_numbers array
                data[key].order_numbers.push(order_number);
            }
    
            const dayKey = `${day}.${month}.${year}`;
            groupData(byDay, 'paymentDate', item.paymentDate, dayKey, itemAmount, item.order_number);
    
            const weekKey = `W${week}.${year}`;
            groupData(byWeek, 'paymentDate', item.paymentDate, weekKey, itemAmount, item.order_number);
    
            const monthKey = `${month}.${year}`;
            groupData(byMonth, 'paymentDate', item.paymentDate, monthKey, itemAmount, item.order_number);
    
            const quarterKey = `Q${Math.ceil(month / 3)}.${year}`;
            groupData(byQuarter, 'paymentDate', item.paymentDate, quarterKey, itemAmount, item.order_number);
    
            if (item.razdel_name) groupData(byRazdel, 'razdel_id', item.razdel_id,  item.razdel_name, itemAmount, item.order_number);
            if (item.tag_name) groupData(byTags, 'tag_id', item.tag_id,  item.tag_name, itemAmount, item.order_number);
            if (item.manager) groupData(byManagers, 'manager_id', item.manager_id,  item.manager, itemAmount, item.order_number);
            if (item.partner) groupData(byPartners, 'partner_id', item.partner_id,  item.partner, itemAmount, item.order_number);
            if (item.product_description) groupData(byProducts, 'product_id', item.product_id,  item.product_description, itemAmount, item.order_number);
            if (item.utm_source) groupData(byUtmSource, 'utm_source', item.utm_source,  item.utm_source, itemAmount, item.order_number);
            if (item.utm_medium) groupData(byUtmMedium, 'utm_medium', item.utm_medium,  item.utm_medium, itemAmount, item.order_number);
            if (item.utm_campaign) groupData(byUtmCampaign, 'utm_campaign', item.utm_campaign,  item.utm_campaign, itemAmount, item.order_number);
            if (item.utm_content) groupData(byUtmContent, 'utm_content', item.utm_content,  item.utm_content, itemAmount, item.order_number);
            if (item.utm_term) groupData(byUtmTerm, 'utm_term', item.utm_term,  item.utm_term, itemAmount, item.order_number);
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

        const allPopulated = groupings.every(grouping => {
            const tableElement = document.getElementById(`reportBy${grouping.name}Table`);
            return tableElement && tableElement.getAttribute('data-populated') === 'true';
        });
        
        if (allPopulated) {
            document.getElementById("dataLoader").style.display = "none";
        }
    }).catch(error => {
        console.error("Error fetching details:", error);
        
        // Hide the loader even if there's an error to avoid an infinite loading state
        document.getElementById("dataLoader").style.display = "none";
    });;
    
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
    
        
        let entries = Object.entries(data);
    
        if (tableId !== 'reportByDayTable' && tableId !== 'reportByWeekTable' && tableId !== 'reportByMonthTable' && tableId !== 'reportByQuarterTable') {
            entries.sort(([, a], [, b]) => b.amount - a.amount);
        }
    
        for (const [key, value] of entries) {
            const row = document.createElement('tr');
            row.addEventListener('click', (event) => {
                showRegistrationDetails(value.order_numbers, value.filters);
            });
        
            
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

        const tableElement = document.getElementById(tableId);
        tableElement.setAttribute('data-populated', 'true');
    }

    function showRegistrationDetails(order_numbers, filters) {

        
        const orderString = order_numbers.join(',');

    
        // Build filters query string
        let filterQueries = [];
        currentFilters.forEach(filter => {
            for (let key in filterMapping) {
                if (filter[filterMapping[key]] && !filterQueries.includes(`${filterMapping[key]}=${filter[filterMapping[key]]}`)) {
                    filterQueries.push(`${filterMapping[key]}=${filter[filterMapping[key]]}`);
                }
            }
        });
        
        filters.forEach(filter => {
            if (filter.paymentDate && !filterQueries.includes(`paymentDate[]=${filter.paymentDate}`)) filterQueries.push(`paymentDate[]=${filter.paymentDate}`);
            if (filter.tag_id && !filterQueries.includes(`tag_id=${filter.tag_id}`)) filterQueries.push(`tag_id=${filter.tag_id}`);
            if (filter.razdel_id && !filterQueries.includes(`razdel_id=${filter.razdel_id}`)) filterQueries.push(`razdel_id=${filter.razdel_id}`);
            if (filter.manager_id && !filterQueries.includes(`manager_id=${filter.manager_id}`)) filterQueries.push(`manager_id=${filter.manager_id}`);
            if (filter.partner_id && !filterQueries.includes(`partner_id=${filter.partner_id}`)) filterQueries.push(`partner_id=${filter.partner_id}`);
            if (filter.product_id && !filterQueries.includes(`product_id=${filter.product_id}`)) filterQueries.push(`product_id=${filter.product_id}`);
            if (filter.utm_source && !filterQueries.includes(`utm_source=${filter.utm_source}`)) filterQueries.push(`utm_source=${filter.utm_source}`);
            if (filter.utm_medium && !filterQueries.includes(`utm_medium=${filter.utm_medium}`)) filterQueries.push(`utm_medium=${filter.utm_medium}`);
            if (filter.utm_campaign && !filterQueries.includes(`utm_campaign=${filter.utm_campaign}`)) filterQueries.push(`utm_campaign=${filter.utm_campaign}`);
            if (filter.utm_content && !filterQueries.includes(`utm_content=${filter.utm_content}`)) filterQueries.push(`utm_content=${filter.utm_content}`);
            if (filter.utm_term && !filterQueries.includes(`utm_term=${filter.utm_term}`)) filterQueries.push(`utm_term=${filter.utm_term}`);
        })

        
        
        // Combine the UIDs and filter queries
        const queryString = `order_numbers=${orderString}&${filterQueries.join('&')}`;
        
        document.getElementById("dataLoader").style.display = "block";
        fetch(`server.php?action=getDetailsByOrder&${queryString}`)
            .then(response => response.json())
            // .then(response => response.text())
            // .then(bodyText => {console.log(bodyText); return JSON.parse(bodyText)})
            .then(data => {
                if (data && data.length) {
                    const detailsModal = document.querySelector("#detailsModal");
                    // Clear previous table data
                    const existingTable = detailsModal.querySelector("table");
                    if(existingTable) {
                        existingTable.remove();
                    }
    
                    // Use the retrieved data
                    data.forEach(registration => {
                        // console.log(registration)
                        // If table does not exist, create it
                        if (!detailsModal.querySelector("table")) {
                            const table = document.createElement('table');
                            const thead = document.createElement('thead');
                            const tbody = document.createElement('tbody');
                            
                            const headers = ['Дата Регистрации', 'Номер Заказа', 'Продукт', 'UID', 'Имя Фамилия', 'Сумма', 'Сумма без Комиссии'];
                            const headerRow = document.createElement('tr');
                            headers.forEach(headerText => {
                                const th = document.createElement('th');
                                th.textContent = headerText;
                                headerRow.appendChild(th);
                            });
                            thead.appendChild(headerRow);
                            table.appendChild(thead);
                            table.appendChild(tbody);
                            
                            detailsModal.querySelector(".modal-content").appendChild(table);
                        }
                    
                        const tableBody = detailsModal.querySelector("tbody");
                    
                        const row = document.createElement('tr');
                    
                        const date = new Date(registration.paymentDate);

                        const day = date.getDate().toString().padStart(2, '0');
                        const month = (date.getMonth() + 1).toString().padStart(2, '0');
                        const year = date.getFullYear();

                        const formattedDate = `${day}.${month}.${year}`;

                        const dateCell = document.createElement('td');
                        dateCell.textContent = formattedDate;
                        row.appendChild(dateCell);

                        const orderNumberCell = document.createElement('td');
                        orderNumberCell.textContent = registration.order_number;
                        row.appendChild(orderNumberCell);

                        const productCell = document.createElement('td');
                        productCell.textContent = registration.order_descr;
                        row.appendChild(productCell);

                        const UIDCell = document.createElement('td');
                        UIDCell.textContent = registration.vk_uid;
                        row.appendChild(UIDCell);

                        const nameSurnameCell = document.createElement('td');
                        nameSurnameCell.textContent = registration.c_name;
                        row.appendChild(nameSurnameCell);

                    
                        const amountCell = document.createElement('td');
                        amountCell.textContent = registration.amount;
                        row.appendChild(amountCell);

                        const amount1Cell = document.createElement('td');
                        amount1Cell.textContent = registration.amount1;
                        row.appendChild(amount1Cell);


                        
                    
                        tableBody.appendChild(row);
                    });
                    // console.log(sum)
    
                    // Display the modal
                    detailsModal.style.display = 'block';

                    document.getElementById("dataLoader").style.display = "none";
                }
            })
            .catch(error => {
                console.error("Error fetching details:", error);

                document.getElementById("dataLoader").style.display = "none";
            });
    }
    

    function clearAllTables() {
        const tables = document.querySelectorAll("#reportTabContent table");
        tables.forEach(table => {
            table.setAttribute('data-populated', 'false');
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


