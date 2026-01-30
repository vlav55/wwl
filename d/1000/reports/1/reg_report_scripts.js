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


    fetch('reg_report_server.php')
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

    formData.forEach((value, key) => {
        console.log(key + ' = ' + value);
    });
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
    };

    // Fetch report data from server
    fetch('reg_report_server.php', {
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
                keyFunc: item => {
                    const date = new Date(item.registrationDate);
                    return `${date.getDate().toString().padStart(2, '0')}.${(date.getMonth() + 1).toString().padStart(2, '0')}.${date.getFullYear()}`;
                }
            },
            {
                name: 'Week', 
                keyFunc: item => {
                    const date = new Date(item.registrationDate);
                    const yearStart = new Date(Date.UTC(date.getFullYear(), 0, 1));
                    const weekNo = Math.ceil((((date - yearStart) / 86400000) + 1) / 7);
                    return `W${weekNo}.${date.getFullYear()}`;
                }
            },
            {
                name: 'Month', 
                keyFunc: item => {
                    const date = new Date(item.registrationDate);
                    return `${date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1}.${date.getFullYear()}`;
                }
            },
            {
                name: 'Quarter', 
                keyFunc: item => {
                    const date = new Date(item.registrationDate);
                    return `Q${Math.ceil((date.getMonth() + 1) / 3)}.${date.getFullYear()}`;
                }
            },
            { name: 'Razdel', keyFunc: item => item.razdel_name },
            { name: 'Tag', keyFunc: item => item.tag_name },
            { name: 'Manager', keyFunc: item => item.manager },
            { name: 'Partner', keyFunc: item => item.partner },
            { name: 'UtmSource', keyFunc: item => item.utm_source },
            { name: 'UtmMedium', keyFunc: item => item.utm_medium },
            { name: 'UtmCampaign', keyFunc: item => item.utm_campaign },
            { name: 'UtmContent', keyFunc: item => item.utm_content },
            { name: 'UtmTerm', keyFunc: item => item.utm_term }
        ];

        const flattenedData = [];
            Object.keys(data).forEach(key => {
                data[key].forEach(item => {
                    item.category = key;
                    flattenedData.push(item);
                });
            });

            const groupData = {};
            groupings.forEach(grouping => {
                groupData[grouping.name] = {};
            });

            function isValidDate(dateString) {
                const date = new Date(dateString);
                return !isNaN(date.getTime());
            }

            const seenUids = new Set();
            const dateBasedGroupings = ['Day', 'Week', 'Month', 'Quarter'];

            data["dates"].forEach(item => {
                if (item && item.distinct_uid_count && item.total_uid_count) {
                    const uniqueId = `${item.uid}-${item.registrationDate}`;
                    
                    if (seenUids.has(uniqueId)) {
                        return;
                    }
                    seenUids.add(uniqueId);

                    dateBasedGroupings.forEach(groupingName => {
                        const keyFunc = groupings.find(g => g.name === groupingName).keyFunc;
                        const key = keyFunc(item);
                        
                        if (key && isValidDate(item.registrationDate)) {
                            if (!groupData[groupingName][key]) {
                                groupData[groupingName][key] = { 
                                    count: 0, 
                                    uids: new Set(), 
                                    sum: 0 
                                };
                            }

                            const currentGroup = groupData[groupingName][key];
                            currentGroup.count += parseInt(item.distinct_uid_count);
                            currentGroup.uids.add(item.uid);
                            currentGroup.sum += parseInt(item.total_uid_count);
                        }
                    });
                }
            });

            // Process other non-date-based groupings
            flattenedData.forEach(item => {
                if (item && item.distinct_uid_count && item.total_uid_count) {
                    groupings.forEach(grouping => {
                        if (!dateBasedGroupings.includes(grouping.name)) {
                            const key = grouping.keyFunc(item);
                            if (key) {
                                if (!groupData[grouping.name][key]) {
                                    groupData[grouping.name][key] = {
                                        count: parseInt(item.distinct_uid_count),
                                        uids: new Set([item.uid]),
                                        sum: parseInt(item.total_uid_count)
                                    };
                                } else {
                                    groupData[grouping.name][key].count += parseInt(item.distinct_uid_count);
                                    groupData[grouping.name][key].sum += parseInt(item.total_uid_count);
                                }
                            }
                        }
                    });
                }
            });

            groupings.forEach(grouping => {
                populateTable(`reportBy${grouping.name}Table`, groupData[grouping.name]);
            });
        });


        function populateTable(tableId, data) {
            const tableBody = document.querySelector(`#${tableId} tbody`);
            tableBody.innerHTML = '';
            
            let totalUniqueCount = 0;
            let totalAmount = 0;
            
            // Determine if the table should be sorted by date or sum
            const isDateBasedReport = ['reportByDayTable', 'reportByWeekTable', 'reportByMonthTable', 'reportByQuarterTable'].includes(tableId);
            
            let sortedData;
            if (isDateBasedReport) {
                sortedData = Object.entries(data);
                sortedData.reverse(); 
            } else {
                // Sort by sum in decrementing order for non-date based reports
                sortedData = Object.entries(data).sort((a, b) => b[1].sum - a[1].sum);
            }
            
            sortedData.forEach(([key, item]) => {
                totalUniqueCount += item.count;
                totalAmount += item.sum;
            
                const row = document.createElement('tr');
            
                const cells = [
                    key,
                    parseInt(item.count),
                    '', // Unique Percentage (placeholder)
                    parseInt(item.sum),
                    ''  // Total Percentage (placeholder)
                ];
            
                cells.forEach(cellText => {
                    const cell = document.createElement('td');
                    cell.textContent = cellText;
                    row.appendChild(cell);
                });
            
                tableBody.appendChild(row);
            });
            
            document.querySelector(`#${tableId}-unique-count`).textContent = totalUniqueCount;
            document.querySelector(`#${tableId}-total-count`).textContent = totalAmount;
            
            // Fill in the percentage cells
            Array.from(tableBody.querySelectorAll('tr')).forEach(row => {
                const uniqueCount = parseInt(row.children[1].textContent);
                const totalCount = parseInt(row.children[3].textContent);
                row.children[2].textContent = ((uniqueCount / totalUniqueCount) * 100).toFixed(2) + '%';
                row.children[4].textContent = ((totalCount / totalAmount) * 100).toFixed(2) + '%';
            });
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
                
                if (totalCountCell) {
                    totalCountCell.textContent = '';
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


