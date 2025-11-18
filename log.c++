#include <iostream>
#include <string>
#include <map>
#include <vector>
using namespace std;

class Employee {
private:
    string id, name, dept, email, password;
    int leaveBalance;
    double salary;
    int attendance;

public:
    Employee() {}
    Employee(string i, string n, string d, string e, string p, double s = 50000) {
        id = i; name = n; dept = d; email = e; password = p;
        salary = s; leaveBalance = 10; attendance = 0;
    }

    string getId() { return id; }
    string getName() { return name; }
    string getDept() { return dept; }
    string getEmail() { return email; }
    string getPassword() { return password; }
    int getLeaveBalance() { return leaveBalance; }
    double getSalary() { return salary; }
    int getAttendance() { return attendance; }

    void markAttendance() { attendance++; }
    void deductLeave() { if (leaveBalance > 0) leaveBalance--; }
    void showDetails() {
        cout << "ID: " << id << " | Name: " << name << " | Dept: " << dept
             << " | Email: " << email << " | Leave Balance: " << leaveBalance
             << " | Salary: " << salary << endl;
    }
};

class HRSystem {
private:
    map<string, Employee> employees;
    map<string, string> leaveRequests; // empID -> reason
    string adminUser = "admin";
    string adminPass = "admin123";

public:
    void adminLogin() {
        string u, p;
        cout << "Enter Admin Username: "; cin >> u;
        cout << "Enter Admin Password: "; cin >> p;
        if (u == adminUser && p == adminPass) {
            cout << "✅ Admin Login Successful!\n";
            adminDashboard();
        } else {
            cout << "❌ Invalid Credentials!\n";
        }
    }

    void employeeLogin() {
        string id, pass;
        cout << "Enter Employee ID: "; cin >> id;
        cout << "Enter Password: "; cin >> pass;
        if (employees.find(id) != employees.end() && employees[id].getPassword() == pass) {
            cout << "✅ Welcome " << employees[id].getName() << "!\n";
            employeeDashboard(id);
        } else {
            cout << "❌ Invalid Employee Credentials!\n";
        }
    }

    void adminDashboard() {
        int choice;
        do {
            cout << "\n--- Admin Dashboard ---\n";
            cout << "1. Add Employee\n2. Edit Employee\n3. Delete Employee\n4. View Employees\n";
            cout << "5. View Attendance\n6. Approve/Reject Leave Requests\n7. Generate Payroll\n8. Logout\n";
            cout << "Enter choice: ";
            cin >> choice;

            switch (choice) {
                case 1: addEmployee(); break;
                case 2: editEmployee(); break;
                case 3: deleteEmployee(); break;
                case 4: viewEmployees(); break;
                case 5: viewAttendance(); break;
                case 6: leaveApproval(); break;
                case 7: generatePayroll(); break;
                case 8: return;
                default: cout << "❌ Invalid choice!\n";
            }
        } while (choice != 8);
    }

    void employeeDashboard(string empId) {
        int choice;
        do {
            cout << "\n--- Employee Dashboard ---\n";
            cout << "1. Mark Attendance\n2. Apply for Leave\n3. View Personal Details\n4. Logout\n";
            cout << "Enter choice: ";
            cin >> choice;

            switch (choice) {
                case 1: employees[empId].markAttendance(); cout << "✅ Attendance Marked!\n"; break;
                case 2: {
                    string reason;
                    cout << "Enter Leave Reason: ";
                    cin.ignore();
                    getline(cin, reason);
                    leaveRequests[empId] = reason;
                    cout << "✅ Leave Requested!\n";
                    break;
                }
                case 3: employees[empId].showDetails(); break;
                case 4: return;
                default: cout << "❌ Invalid choice!\n";
            }
        } while (choice != 4);
    }

    void addEmployee() {
        string id, name, dept, email, pass;
        cout << "Enter ID: "; cin >> id;
        cout << "Enter Name: "; cin >> name;
        cout << "Enter Department: "; cin >> dept;
        cout << "Enter Email: "; cin >> email;
        cout << "Set Password: "; cin >> pass;
        employees[id] = Employee(id, name, dept, email, pass);
        cout << "✅ Employee Added!\n";
    }

    void editEmployee() {
        string id; cout << "Enter ID to Edit: "; cin >> id;
        if (employees.find(id) != employees.end()) {
            string name, dept, email;
            cout << "Enter New Name: "; cin >> name;
            cout << "Enter New Dept: "; cin >> dept;
            cout << "Enter New Email: "; cin >> email;
            employees[id] = Employee(id, name, dept, email, employees[id].getPassword());
            cout << "✅ Employee Updated!\n";
        } else cout << "❌ Employee Not Found!\n";
    }

    void deleteEmployee() {
        string id; cout << "Enter ID to Delete: "; cin >> id;
        if (employees.erase(id)) cout << "✅ Employee Deleted!\n";
        else cout << "❌ Employee Not Found!\n";
    }

    void viewEmployees() {
        cout << "\n--- Employee Records ---\n";
        for (auto &e : employees) {
            e.second.showDetails();
        }
    }

    void viewAttendance() {
        cout << "\n--- Attendance Records ---\n";
        for (auto &e : employees) {
            cout << e.second.getId() << " : " << e.second.getAttendance() << " days\n";
        }
    }

    void leaveApproval() {
        for (auto &lr : leaveRequests) {
            cout << "Employee " << lr.first << " requested leave: " << lr.second << endl;
            char decision; cout << "Approve (y/n)? "; cin >> decision;
            if (decision == 'y') {
                employees[lr.first].deductLeave();
                cout << "✅ Leave Approved!\n";
            } else {
                cout << "❌ Leave Rejected!\n";
            }
        }
        leaveRequests.clear();
    }

    void generatePayroll() {
        cout << "\n--- Payroll Report ---\n";
        for (auto &e : employees) {
            double dailySalary = e.second.getSalary() / 30;
            double finalSalary = dailySalary * e.second.getAttendance();
            cout << e.second.getId() << " - " << e.second.getName()
                 << " | Salary: " << finalSalary << endl;
        }
    }
};

// Main Function
int main() {
    HRSystem system;
    int choice;
    do {
        cout << "\n==== HR Management System ====\n";
        cout << "1. Admin Login\n2. Employee Login\n3. Exit\n";
        cout << "Enter choice: ";
        cin >> choice;

        switch (choice) {
            case 1: system.adminLogin(); break;
            case 2: system.employeeLogin(); break;
            case 3: cout << "Exiting...\n"; break;
            default: cout << "❌ Invalid choice!\n";
        }
    } while (choice != 3);
    return 0;
}